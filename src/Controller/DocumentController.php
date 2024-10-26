<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\DemandeurRepository;
use App\Repository\DocumentRepository;
use App\Repository\EtablissementRepository;
use App\Repository\InstitutRepository;
use App\Repository\UserRepository;
use App\services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DocumentController extends AbstractController
{
    /**
     * @var DocumentRepository
     */
    private DocumentRepository $documentRepository;


    function __construct(DocumentRepository $documentRepository,)
    {
        $this->documentRepository = $documentRepository;
    }


    #[Route('/api/create-document', name: 'api_create_document', methods: ['POST'])]
    public function createDocument(
        Request $request,
        UserRepository
        $userRepository,
        MailService $mailService
    ): Response {

        // $data = json_decode($request->getContent(), true);

        // // dd($file = $request->files->get('document'));
        // // dd($file = $request->request->get('etudiant_id'));
        // $etudiantId = $request->request->get('etudiant_id');

        // $typeDocument = $request->request->get('type_document');
        // $dateObtention = $request->request->get('date_obtention');
        // $anneeObtention = $request->request->get('annee_obtention');
        // $etablissement_id = $request->request->get('etablissement_id');


        // $document = new Document();

        // $etudiant = $userRepository->find(intval($etudiantId));

        // if (!$etudiant || !in_array('ROLE_ETUDIANT', $etudiant->getRoles())) {
        //     return $this->json(['error' => 'Invalide étudiant'], Response::HTTP_BAD_REQUEST);
        // }

        // $etablissement = $this->etablissementRepository->find(intval($etablissement_id));
        // if (!$etablissement) {
        //     return $this->json(['error' => 'Invalide etablissement'], Response::HTTP_BAD_REQUEST);
        // } else {
        //     $document->setEtablissement($etablissement);
        // }



        // $file = $request->files->get('document');
        // if ($file) {

        //     $document->setIntitule($typeDocument . '-' . $anneeObtention);

        //     $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //     $newFilename = $document->getIntitule() . '.' . $file->guessExtension();
        //     $file->move($this->getParameter('document_directory'), $newFilename);

        //     $document->setCodeAdn($document->generateCode());
        //     $document->setUrl($this->getParameter('document_directory') . '/' . $newFilename);

        //     $document->setStatut('Créé');
        //     $document->setEtudiant($etudiant);
        //     $document->setTypeDocument($typeDocument);
        //     $date = \DateTime::createFromFormat('Y-m-d', $dateObtention);
        //     $document->setDateObtention($date);
        //     $document->setAnneeObtention($anneeObtention);


        //     $this->documentRepository->save($document, true);
        //     $response = $mailService->sendDocumentCreationEmail($etudiant, $document, $file);
        //     if ($response != 200) {
        //         return $this->json(['error' => $response], Response::HTTP_BAD_REQUEST);
        //     }
        //     return $this->json(['document' => $document], Response::HTTP_CREATED);
        // }

        return $this->json(['error' => 'Document invalide'], Response::HTTP_BAD_REQUEST);
    }

    // fonction pour verifier un document
    #[Route('/api/verification-document', name: 'api_verification_document', methods: ['POST'])]
    public function verificationDocument(
        Request $request,
        DocumentRepository $documentRepository,
        InstitutRepository $institutRepository
    ): Response {

        $data = json_decode($request->getContent(), true);
        $institutId = $data['institut_id'] ?? null;
        $codeAdn = $data['codeAdn'] ?? null;

        if ($institutId === null || $codeAdn === null) {
            return $this->json(['error' => 'Institut ID et Code ADN requis'], Response::HTTP_BAD_REQUEST);
        }

        // Vérifier si l'institut existe
        $institut = $institutRepository->find($institutId);
        if (!$institut) {
            return $this->json(['error' => 'Institut non trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le document existe
        $document = $documentRepository->findOneBy(['codeAdn' => $codeAdn]);
        if (!$document) {
            return $this->json(['error' => 'Document non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $document->setStatut('verified');
        $documentRepository->save($document, true);

        return $this->json(
            ['document' => $document],
            Response::HTTP_OK
        );
    }


    #[Route('/api/confirmation-demande', name: 'api_confirmation_demande_document', methods: ['POST'])]
    public function confirmationDemande(
        Request $request,
        DocumentRepository $documentRepository,
        InstitutRepository $institutRepository,
        DemandeRepository $demandeRepository,
        MailService $mailService
    ): Response {
        // Récupérer les données de la requête


        $demandeId = $request->request->get('demande_id') ?? null;
        $institutId = $request->request->get('institut_id') ?? null;
        $typeDocument = $request->request->get('type_document') ?? null;
        $dateObtention = $request->request->get('date_obtention') ?? null;
        $anneeObtention = $request->request->get('annee_obtention') ?? null;
        $resultatDemande = $request->request->get('resultat_demande') ?? null;
        $mention = $request->request->get('mention') ?? null;


        // Vérifier si l'établissement existe
        $institut = $institutRepository->find(intval($institutId));
        if (!$institut) {
            return $this->json(['error' => 'Institut non  trouvé' . $institutId], Response::HTTP_NOT_FOUND);
        }
        $demande = $demandeRepository->find(intval($demandeId));
        if (!$demande) {
            return $this->json(['error' => 'Demande non trouvé'], Response::HTTP_NOT_FOUND);
        }
        // if (!$request->files->get('document')  || $resultatDemande != 'Accepté') {
        //     return $this->json(['error' => 'Document non valable'], Response::HTTP_NOT_FOUND);
        // }
        // Créer un nouveau document
        $document = new Document();

        // Gérer le fichier téléchargé
        /** @var UploadedFile|null $file */
        $file = $request->files->get('document');

        if ($file) {

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = sprintf(
                '%s-%s-%s-%s.%s',
                str_replace(' ', '-', strtolower($typeDocument)),
                $demande->getIntitule() ? str_replace(' ', '-', strtolower($demande->getIntitule())) : date('YmdHis'),
                $anneeObtention,
                $demande->getDemandeur()->getName() ? str_replace(' ', '-', strtolower($demande->getDemandeur()->getName())) : date('YmdHis'),
                $file->guessExtension()
            );

            $file->move($this->getParameter('document_directory'), $newFilename);

            $demande->setResultat($resultatDemande);


            $demandeRepository->save($demande, true);
            $document->setIntitule(
                $typeDocument . '-' .
                    $anneeObtention . '-' .
                    str_replace(' ', '-', strtolower($demande->getIntitule()))
            );
            $document->setCodeAdn($document->generateCode());
            $document->setMention($mention);
            $document->setUrl($this->getParameter('document_directory') . '/' . $newFilename);
            $document->setStatut('Créé');
            $document->setTypeDocument($typeDocument);
            $document->setDateObtention(new \DateTime());
            $document->setAnneeObtention($anneeObtention);
            // if ($dateObtention) {
            //     try {
            //         $date = \DateTime::createFromFormat('Y-m-d', $dateObtention);
            //         if ($date) {
            //             $document->setDateObtention($date);
            //         }
            //     } catch (\Exception $e) {
            //         return $this->json(['error' => 'Format de date d\'obtention invalide'], 400);
            //     }
            // }

            // if ($anneeObtention) {
            //     $document->setAnneeObtention($anneeObtention);
            // }


            $documentRepository->save($document, true);

            $detailsDemande = "Votre demande a été acceptée avec succès.";
            $resul = $mailService->sendDemandeResultToDemandeur(
                $demande->getDemandeur(),
                $demande->getResultat(),
                $detailsDemande
            );

            if ($resul == 400) {
                return $this->json(['error' => 'Erreur lors de l\'envoi de l\'email'], 400);
            }
            return $this->json(['document' => $document], Response::HTTP_CREATED);
        }

        return $this->json(['error' => 'Fichier document invalide'], 400);
    }


    // get file by id
    #[Route('/api/documents/file/{id}', name: 'api_get_document_file', methods: ['GET'])]
    public function getFileDocument(int $id, DocumentRepository $documentRepository): Response
    {
        $document = $documentRepository->find($id);

        if (!$document) {
            return $this->json(['error' => 'Document non valable'], Response::HTTP_NOT_FOUND);
        }
        try {

            $file = base64_encode(file_get_contents($document->getUrl()));
            return $this->json($file, 200, [], ['groups' => 'document_get']);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->json(null, Response::HTTP_OK);
        }
    }

    // get all doculent by id demandeur
    #[Route('/api/documents/demandeur/{id}', name: 'api_get_document_by_demandeur', methods: ['GET'])]
    public function getDocumentByDemandeur(
        int $id,
        EntityManagerInterface $entityManager,
        DocumentRepository $documentRepository
    ): Response {
        $qb = $entityManager->createQueryBuilder();
        $qb->select('d')
            ->from('App\Entity\Document', 'd')
            ->join('d.demande', 'dem')
            ->join('dem.demandeur', 'demandeur')
            ->where('demandeur.id = :demandeurId')
            ->setParameter('demandeurId', $id);

        $documents = $qb->getQuery()->getResult();

        if (empty($documents)) {
            return $this->json(
                [],
                Response::HTTP_OK
            );
        }

        return $this->json(
            $documents,
            Response::HTTP_OK,

            ['groups' => 'document:list']
        );
    }



    #[Route('/api/documents/institut/{id}', name: 'api_get_institut_documents', methods: ['GET'])]
    public function getInstitutDocuments(
        $id,
        InstitutRepository $institutRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $institut = $institutRepository->find($id);

        if (!$institut) {
            return $this->json(['message' => 'Institut not found'], Response::HTTP_NOT_FOUND);
        }

        $qb = $entityManager->createQueryBuilder();
        $qb->select('d')
            ->from('App\Entity\Document', 'd')
            ->join('d.demande', 'dem')
            ->where('dem.institut = :institut')
            ->setParameter('institut', $institut);

        $documents = $qb->getQuery()->getResult();

        if (empty($documents)) {
            return $this->json([], Response::HTTP_OK);
        }

        $serializedDocuments = array_map(
            function ($document) {
                return [
                    'id' => $document->getId(),
                    'codeAdn' => $document->getCodeAdn(),
                    'typeDocument' => $document->getTypeDocument(),
                    'dateObtention' => $document->getDateObtention()->format('Y-m-d H:i:s'),
                    'anneeObtention' => $document->getAnneeObtention(),
                    'statut' => $document->getStatut(),
                    'intitule' => $document->getIntitule(),
                    'url' => $document->getUrl(),
                    'isDeleted' => $document->isIsDeleted(),
                    'demande' => [
                        'id' => $document->getDemande()->getId(),
                        'intitule' => $document->getDemande()->getIntitule(),
                        'dateDemande' => $document->getDemande()->getDateDemande()->format('Y-m-d H:i:s'),
                        'resultat' => $document->getDemande()->getResultat(),
                        'isDeleted' => $document->getDemande()->isIsDeleted(),
                        'demandeur' => [
                            'id' => $document->getDemande()->getDemandeur()->getId(),
                            'name' => $document->getDemande()->getDemandeur()->getName(),
                            'email' => $document->getDemande()->getDemandeur()->getEmail(),

                        ]
                    ],
                ];
            },
            $documents
        );

        return $this->json($serializedDocuments, Response::HTTP_OK);
    }
}
