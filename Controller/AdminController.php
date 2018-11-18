<?php

namespace SchoolIT\IdpExchangeBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use SchoolIT\CommonBundle\Form\ConfirmType;
use SchoolIT\IdpExchangeBundle\Entity\UpdateUser;
use SchoolIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller {

    const ITEMS_PER_PAGE = 25;

    private $manager;

    public function __construct(SynchronizationManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/exchange", name="idp_exchange_admin")
     */
    public function index(Request $request) {
        $page = $request->query->get('page', 1);

        if(!is_numeric($page) || $page < 0) {
            $page = 1;
        }

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('u')
            ->from(UpdateUser::class, 'u')
            ->orderBy('u.dateTime', 'asc')
            ->setFirstResult(($page - 1) * static::ITEMS_PER_PAGE)
            ->setMaxResults(static::ITEMS_PER_PAGE);

        $paginator = new Paginator($queryBuilder->getQuery());
        $count = $paginator->count();
        $pages = 0;

        if($count > 0) {
            $pages = ceil((float)$count / static::ITEMS_PER_PAGE);
        }

        $lastSync = $this->manager->getLastSync();

        return $this->render('@IdpExchange/index.html.twig', [
            'lastSync' => $lastSync,
            'pages' => $pages,
            'page' => $page,
            'count' => $count,
            'users' => $paginator
        ]);
    }

    /**
     * @Route("/admin/exchange/clear", name="idp_exchange_clear_admin")
     */
    public function clear(Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'idp_exchange.clear.confirm',
            'header' => 'idp_exchange.clear.label'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->manager->reset();

            $this->addFlash('success', 'idp_exchange.clear.success');
            return $this->redirectToRoute('idp_exchange_admin');
        }

        return $this->render('@IdpExchange/clear.html.twig', [
            'form' => $form->createView()
        ]);
    }
}