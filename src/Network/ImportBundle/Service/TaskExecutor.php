<?php

namespace Network\ImportBundle\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TaskExecutor extends PageRequestor
{
    protected $container;
    protected $userManager;
    const PAGE_SIZE = 50;

    public function __construct(ContainerInterface $container, $userManager)
    {
        $this->container = $container;
        $this->userManager = $userManager;
    }

    public function execute()
    {
        $em = $this->container->get('doctrine')->getManager();
        $executor = $this->container->get('request_executor');
        $processor = $this->container->get('response_processor');
        $loader = $this->container->get('content_loader');
        $endpoints = $this->container->getParameter('endpoints');
        $query = $em->createQueryBuilder()
                    ->select('t')
                    ->from('NetworkStoreBundle:SyncTask', 't')
                    ->andWhere(':timestamp - t.lastUpdateTimestamp > 82800')
                    ->setParameter('timestamp', (new \DateTime('now'))->getTimestamp());
        $countQuery = $em->createQueryBuilder('t')
                         ->select('count(t.id)')
                         ->from('NetworkStoreBundle:SyncTask', 't')
                         ->andWhere(':timestamp - t.lastUpdateTimestamp > 82800')
                         ->setParameter('timestamp', (new \DateTime('now'))->getTimestamp()) ;
        $pages = self::countPages($countQuery, static::PAGE_SIZE);
        $i = 1;
        $now = new \DateTime('now');
        while ($i <= $pages) {
            $page = self::paginate($query, static::PAGE_SIZE, $i);
            $tasks = $page->getQuery()
                          ->getResult();
            if ($tasks) {
                $userIds =  array_map(create_function('$o', 'return $o->getUserId();'), $tasks);
                $usersQuery = $em->createQueryBuilder()
                                 ->select('u')
                                 ->from('NetworkStoreBundle:User', 'u')
                                 ->andWhere('u.id IN (:users)')
                                 ->setParameter('users', $userIds)
                                 ->getQuery();
                $users = $usersQuery->getResult();
                $em->clear();
                $userMap = array();
                foreach ($users as $user) {
                    $userMap[$user->getId()] = $user;
                }
                foreach ($tasks as $task) {
                    $config = $task->getParams();
                    $params = array();
                    $endpointName = $task->getEndpoint();
                    $endpoint = $endpoints[$endpointName];
                    $service = $endpoint['owner'];
                    $user = $userMap[$task->getUserId()];
                    $paths = $endpoint['paths'];
                    $scheduleParams = $endpoint['schedule_params'];
                    $getter = 'get' . ucfirst($service);
                    foreach ($paths as $path) {
                        $pathGetter = $getter . $path;
                        $params[$path] = $user->$pathGetter();
                    }
                    $getter = 'get';
                    foreach ($scheduleParams as $p) {
                        $pathGetter = $getter.$p;
                        $params[$p] = $task->$pathGetter();
                    }
                    $response = $executor->request($endpoint['method'], $endpoint['url'], null, $params);
                    $offset = $processor->process($service, $response, $config, $endpoint['json_root']);
                    //TODO optimize this query
                    $q = $em->createQueryBuilder()
                            ->update('NetworkStoreBundle:SyncTask', 'u')
                            ->set('u.lastUpdateTimestamp', '?1')
                            ->set('u.offset', '?2')
                            ->where('u.id = :id')
                            ->setParameter('id', $task->getId())
                            ->setParameter('1', $now->getTimestamp())
                            ->setParameter('2', $task->getOffset() + $offset)
                            ->getQuery();
                    $q->execute();
                }
                $em->flush();
                $em->clear();
            }
            $i++;
        }
        $loader->loadContent();
    }
}
