<?php

namespace App\Domain\Reporting\Controller;

use App\Domain\Reporting\Dictionary\LogJournalActionDictionary;
use App\Domain\Reporting\Dictionary\LogJournalSubjectDictionary;
use App\Domain\Reporting\Generator\LogJournalLinkGenerator;
use App\Domain\Reporting\Model\LogJournal as LogModel;
use App\Domain\Reporting\Repository\LogJournal;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class JournalisationController extends AbstractController
{
    /**
     * @var LogJournal
     */
    private $logRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LogJournalLinkGenerator
     */
    private $logJournalLinkGenerator;

    public function __construct(LogJournal $logRepository, RouterInterface $router, LogJournalLinkGenerator $logJournalLinkGenerator)
    {
        $this->logRepository           = $logRepository;
        $this->router                  = $router;
        $this->logJournalLinkGenerator = $logJournalLinkGenerator;
    }

    public function indexAction()
    {
        return $this->render('Reporting/Journalisation/list.html.twig', [
            'totalItem' => $this->logRepository->countLogs(),
            'route'     => $this->router->generate('reporting_journalisation_list_datatables'),
        ]);
    }

    public function listDataTables(Request $request)
    {
        $draw       = $request->request->get('draw');
        $first      = $request->request->get('start');
        $maxResults = $request->request->get('length');
        $orders     = $request->request->get('order');
        $columns    = $request->request->get('columns');

        $orderColumn = $this->getCorrespondingLabelFromkey($orders[0]['column']);
        $orderDir    = $orders[0]['dir'];

        $searches = [];
        foreach ($columns as $column) {
            if ('' !== $column['search']['value']) {
                $searches[$column['data']] = $column['search']['value'];
            }
        }

        /** @var Paginator $logs */
        $logs  = $this->logRepository->findPaginated($first, $maxResults, $orderColumn, $orderDir, $searches);
        $count = $this->logRepository->countLogs();

        $reponse = [
            'draw'            => $draw,
            'recordsTotal'    => $count,
            'recordsFiltered' => count($logs),
            'data'            => [],
        ];

        /** @var LogModel $log */
        foreach ($logs as $log) {
            $reponse['data'][] = [
                'utilisateur'  => $log->getUser()->getFullName(),
                'collectivite' => $log->getCollectivity()->getName(),
                'date'         => date_format($log->getDate(), 'd-m-Y H:i:s'),
                'subject'      => LogJournalSubjectDictionary::getSubjectLabelFromSubjectType($log->getSubjectType()),
                'action'       => LogJournalActionDictionary::getActions()[$log->getAction()],
                'subjectId'    => $log->getLastKnownName(),
                'link'         => $this->generateLinkCellContent($this->logJournalLinkGenerator->getLink($log)),
            ];
        }

        $jsonResponse = new JsonResponse();
        $jsonResponse->setJson(json_encode($reponse));

        return $jsonResponse;
    }

    private function generateLinkCellContent(string $content)
    {
        if (LogJournalLinkGenerator::DELETE_LABEL === $content) {
            return $content;
        }

        return '<a href="' . $content . '">Voir</a>';
    }

    private function getCorrespondingLabelFromkey(string $key)
    {
        $array = [
            '0' => 'utilisateur',
            '1' => 'collectivite',
            '2' => 'date',
            '3' => 'subject',
            '4' => 'action',
            '5' => 'subjectId',
            '6' => 'link',
        ];

        return \array_key_exists($key, $array) ? $array[$key] : null;
    }
}
