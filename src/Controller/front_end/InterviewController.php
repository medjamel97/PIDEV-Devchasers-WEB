<?php

namespace App\Controller\front_end;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Interview;
use App\Entity\Societe;
use App\Entity\User;
use App\Form\InterviewType;
use DateTime;
use DateTimeZone;
use Error;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("interview/")
 */
class InterviewController extends AbstractController
{

    /**
     * @Route("")
     */
    public function afficherToutInterview(Request $request, PaginatorInterface $paginator): Response
    {
        $interviews = $this->getDoctrine()->getRepository(Interview::class)->findBy([], [
            'dateCreation' => 'DESC',
        ]);

        return $this->render('front_end/societe/offre_de_travail/interview/afficher_tout.html.twig', [
            'offreDeTravail' => null,
            'societe' => null,
            'totalInterviews' => count($interviews),
            'interviews' => $paginator->paginate(
                $interviews,
                $request->query->getInt('page', 1), 6
            ),
            'form' => null,
            'interviewId' => null
        ]);
    }

    /**
     * @Route("offre_de_travail/{offreDeTravailId}", name="interview_par_offre")
     */
    public function afficherInterviewParOffre(Request $request, PaginatorInterface $paginator): Response
    {
        $page = $request->get('page');
        $interviewId = $request->get('interviewId');
        $offreDeTravailId = $request->get('offreDeTravailId');

        $interviews = $this->getDoctrine()->getRepository(Interview::class)->findByOffre($offreDeTravailId);

        if ($page) {
            if ($interviewId) {
                $index = 1;
                foreach ($interviews as $interview) {
                    if ($interview->getId() == $interviewId) {
                        break;
                    }
                    $index++;
                }
                $pageAct = intdiv($index - 1, 6) + 1;

                if ($page != $pageAct) {
                    return $this->redirectToRoute('interview_par_offre', [
                        'offreDeTravailId' => $offreDeTravailId,
                        'page' => $page
                    ]);
                }
            }
        }

        if ($interviewId) {
            if (!$page) {
                $index = 1;
                foreach ($interviews as $interview) {
                    if ($interview->getId() == $interviewId) {
                        break;
                    }
                    $index++;
                }
                $page = intdiv($index - 1, 6) + 1;

                return $this->redirectToRoute('interview_par_offre', [
                    'offreDeTravailId' => $offreDeTravailId,
                    'interviewId' => $interviewId,
                    'page' => $page
                ]);
            }
        }

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->findOneBy([
                'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($offreDeTravailId),
                'candidat' => $user->getCandidat()
            ]);

            if ($candidatureOffre) {
                $interview = new Interview();
                $form = $this->createForm(InterviewType::class, $interview)
                    ->add('submit', SubmitType::class)
                    ->handleRequest($request);

                if ($form->isSubmitted() && !$form->isValid()) {
                    $this->addFlash("error", "Erreur de saisie");
                }

                if ($form->isSubmitted() && $form->isValid()) {
                    $interview = $form->getData();
                    if ($candidatureOffre) $interview->setCandidatureOffre($candidatureOffre);

                    try {
                        $interview->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));
                    } catch (Exception $e) {
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($interview);
                    $entityManager->flush();

                    return $this->redirectToRoute('interview_par_offre', [
                        'offreDeTravailId' => $offreDeTravailId,
                        'interviewId' => $interview->getId(),
                    ]);
                }
            } else {
                $form = null;
            }
        } else {
            $form = null;
        }

        if ($form) {
            $formView = $form->createView();
        } else {
            $formView = null;
        }

        return $this->render('front_end/societe/offre_de_travail/interview/afficher_tout.html.twig', [
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($offreDeTravailId),
            'societe' => null,
            'totalInterviews' => count($interviews),
            'interviews' => $paginator->paginate(
                $interviews,
                $request->query->getInt('page', 1), 6
            ),
            'form' => $formView,
            'interviewId' => $interviewId
        ]);
    }

    /**
     * @Route("societe/{societeId}", name="afficher_interview_par_societe")
     */
    public function afficherInterviewParSociete($societeId, Request $request, PaginatorInterface $paginator): Response
    {
        $interviews = $this->getDoctrine()->getRepository(Interview::class)->findBySociete($societeId);

        return $this->render('front_end/societe/offre_de_travail/interview/afficher_tout.html.twig', [
            'offreDeTravail' => null,
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($societeId),
            'totalInterviews' => count($interviews),
            'interviews' => $paginator->paginate(
                $interviews,
                $request->query->getInt('page', 1), 6
            ),
            'form' => null,
            'interviewId' => null
        ]);
    }

    /**
     * @Route("{candidatureOffreId}/ajouter")
     * @throws Exception
     */
    public function ajouterInterview(Request $request, $candidatureOffreId)
    {

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($candidatureOffreId);

        $interview = new Interview();

        if (($user == $candidatureOffre->getCandidat()->getUser()) && ($candidatureOffre->getEtat() == "accepté")) {

            $form = $this->createForm(InterviewType::class, $interview)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $interview = $form->getData();

                if ($candidatureOffre) $interview->setCandidatureOffre($candidatureOffre);
                $interview->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($interview);
                $entityManager->flush();

                return $this->redirectToRoute('interview_par_offre', [
                    'offreDeTravailId' => $candidatureOffre->getOffreDeTravail()->getId(),
                    'interviewId' => $interview->getId(),
                ]);
            }

            return $this->render('front_end/societe/offre_de_travail/interview/manipuler.html.twig', [
                'candidatureOffre' => $candidatureOffre,
                'interview' => $interview,
                'form' => $form->createView(),
                'manipulation' => "Ajouter"
            ]);
        } else {
            throw new Error("Access denied");
        }
    }

    /**
     * @Route("{idInterview}/modifier")
     */
    public function modifierInterview(Request $request, $idInterview)
    {

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $interview = $this->getDoctrine()->getRepository(Interview::class)->find($idInterview);
        $candidatureOffre = $interview->getCandidatureOffre();

        if ($user) {
            if ($user == $interview->getCandidatureOffre()->getCandidat()->getUser()) {

                $form = $this->createForm(InterviewType::class, $interview)
                    ->add('submit', SubmitType::class)
                    ->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $interview = $form->getData();
                    if ($candidatureOffre) $interview->setCandidatureOffre($candidatureOffre);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($interview);
                    $entityManager->flush();

                    return $this->redirectToRoute('interview_par_offre', [
                        'offreDeTravailId' => $candidatureOffre->getOffreDeTravail()->getId(),
                        'interviewId' => $interview->getId(),
                    ]);
                }

                return $this->render('front_end/societe/offre_de_travail/interview/manipuler.html.twig', [
                    'candidatureOffre' => $candidatureOffre,
                    'interview' => $interview,
                    'form' => $form->createView(),
                    'manipulation' => "Modifier"
                ]);
            } else {
                throw new Error("Vous ne pouvez pas modifier les interviews des autres candidats !");
            }
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("{idInterview}/supprimer", name="supprimer_interview")
     */
    public function supprimerInterview($idInterview): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $interview = $manager->getRepository(Interview::class)->find($idInterview);
        $manager->remove($interview);
        $manager->flush();

        $this->addFlash('success', 'Votre interview a été supprimé.');
        return $this->redirect('/interview/offre_de_travail/' . $interview->getCandidatureOffre()->getOffreDeTravail()->getId());
    }

    /**
     * @Route("recherche_par_societe")
     */
    public function rechercheParSociete(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $valeurRecherche = $request->get('valeur-recherche');
        if ($valeurRecherche != null) {
            $societe = $this->getDoctrine()->getRepository(Societe::class)->findStartingWith($valeurRecherche);
            if ($societe) {
                $interviews = $this->getDoctrine()->getRepository(Interview::class)->findBySociete($societe->getId());
            } else {
                return new Response(null);
            }
            if ($interviews) {
                $i = 0;
                $jsonContent = null;
                foreach ($interviews as $interview) {
                    $jsonContent[$i]['id'] = $interview->getId();
                    $jsonContent[$i]['idCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getId();
                    $jsonContent[$i]['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
                    $jsonContent[$i]['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
                    $jsonContent[$i]['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
                    $jsonContent[$i]['idSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getId();
                    $jsonContent[$i]['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
                    $jsonContent[$i]['idOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getId();
                    $jsonContent[$i]['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
                    $jsonContent[$i]['salaire'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSalaire();
                    $jsonContent[$i]['difficulte'] = $interview->getDifficulte();
                    $jsonContent[$i]['description'] = $interview->getDescription();
                    $jsonContent[$i]['dateCreation'] = $interview->getDateCreation()->format('H:i - d/M/Y');

                    if ($interview->getCandidatureOffre()->getCandidat()->getUser() == $user) {
                        $jsonContent[$i]['isProperty'] = true;
                    } else {
                        $jsonContent[$i]['isProperty'] = false;
                    }

                    $jsonContent[$i]['isAdmin'] = false;
                    if ($user) {
                        foreach ($user->getRoles() as $role) {
                            if ($role == "ROLE_ADMIN") {
                                $jsonContent[$i]['isAdmin'] = true;
                            }
                        }
                    }

                    $i++;
                }
                return new Response(json_encode($jsonContent));
            } else {
                return new Response(null);
            }
        } else {
            return new Response(null);
        }
    }

    /**
     * @Route("recherche_par_difficulte")
     */
    public function rechercheParDifficulte(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $societeId = (int)$request->get('societeId');
        $offreDeTravailId = (int)$request->get('offreDeTravailId');
        $difficulte = (int)$request->get('valeurRecherche');

        $interviews = null;

        if ($societeId) {
            $interviews = $this->getDoctrine()->getRepository(Interview::class)->findBySocieteAndDifficulte(
                $societeId, $difficulte
            );
        } elseif ($offreDeTravailId) {
            $interviews = $this->getDoctrine()->getRepository(Interview::class)->findByOffreAndDifficulte(
                $offreDeTravailId, $difficulte
            );
        } else {
            $interviews = $this->getDoctrine()->getRepository(Interview::class)->findBy([
                'difficulte' => $difficulte
            ], [
                'dateCreation' => 'DESC'
            ]);
        }

        if ($interviews) {
            $i = 0;
            $jsonContent = null;
            foreach ($interviews as $interview) {
                $jsonContent[$i]['id'] = $interview->getId();
                $jsonContent[$i]['idCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getId();
                $jsonContent[$i]['nomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getNom();
                $jsonContent[$i]['prenomCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getPrenom();
                $jsonContent[$i]['idPhotoCandidat'] = $interview->getCandidatureOffre()->getCandidat()->getIdPhoto();
                $jsonContent[$i]['idSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getId();
                $jsonContent[$i]['nomSociete'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
                $jsonContent[$i]['idOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getId();
                $jsonContent[$i]['nomOffre'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getNom();
                $jsonContent[$i]['salaire'] = $interview->getCandidatureOffre()->getOffreDeTravail()->getSalaire();
                $jsonContent[$i]['difficulte'] = $interview->getDifficulte();
                $jsonContent[$i]['description'] = $interview->getDescription();
                $jsonContent[$i]['dateCreation'] = $interview->getDateCreation()->format('H:i - d/M/Y');

                if ($interview->getCandidatureOffre()->getCandidat()->getUser() == $user) {
                    $jsonContent[$i]['isProperty'] = true;
                } else {
                    $jsonContent[$i]['isProperty'] = false;
                }

                $jsonContent[$i]['isAdmin'] = false;
                if ($user) {
                    foreach ($user->getRoles() as $role) {
                        if ($role == "ROLE_ADMIN") {
                            $jsonContent[$i]['isAdmin'] = true;
                        }
                    }
                }

                $i++;
            }
            return new Response(json_encode($jsonContent));
        } else {
            return new Response(null);
        }
    }
}
