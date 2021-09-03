<?php

namespace App\Controller\front_end;

use App\Entity\CandidatureOffre;
use App\Entity\OffreDeTravail;
use App\Entity\Revue;
use App\Entity\Societe;
use App\Entity\User;
use App\Form\RevueType;
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
 * @Route("revue/")
 */
class RevueController extends AbstractController
{

    /**
     * @Route("")
     */
    public function afficherToutRevue(Request $request, PaginatorInterface $paginator): Response
    {
        $revues = $this->getDoctrine()->getRepository(Revue::class)->findBy([], [
            'dateCreation' => 'DESC',
        ]);

        return $this->render('front_end/societe/offre_de_travail/revue/afficher_tout.html.twig', [
            'offreDeTravail' => null,
            'societe' => null,
            'totalRevues' => count($revues),
            'revues' => $paginator->paginate(
                $revues,
                $request->query->getInt('page', 1), 6
            ),
            'form' => null,
            'revueId' => null
        ]);
    }

    /**
     * @Route("offre_de_travail/{offreDeTravailId}", name="revue_par_offre")
     */
    public function afficherRevueParOffre(Request $request, PaginatorInterface $paginator): Response
    {
        $page = $request->get('page');
        $revueId = $request->get('revueId');
        $offreDeTravailId = $request->get('offreDeTravailId');

        $revues = $this->getDoctrine()->getRepository(Revue::class)->findByOffre($offreDeTravailId);

        if ($page) {
            if ($revueId) {
                $index = 1;
                foreach ($revues as $revue) {
                    if ($revue->getId() == $revueId) {
                        break;
                    }
                    $index++;
                }
                $pageAct = intdiv($index - 1, 6) + 1;

                if ($page != $pageAct) {
                    return $this->redirectToRoute('revue_par_offre', [
                        'offreDeTravailId' => $offreDeTravailId,
                        'page' => $page
                    ]);
                }
            }
        }

        if ($revueId) {
            if (!$page) {
                $index = 1;
                foreach ($revues as $revue) {
                    if ($revue->getId() == $revueId) {
                        break;
                    }
                    $index++;
                }
                $page = intdiv($index - 1, 6) + 1;

                return $this->redirectToRoute('revue_par_offre', [
                    'offreDeTravailId' => $offreDeTravailId,
                    'revueId' => $revueId,
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
                $revue = new Revue();
                $form = $this->createForm(RevueType::class, $revue)
                    ->add('submit', SubmitType::class)
                    ->handleRequest($request);

                if ($form->isSubmitted() && !$form->isValid()) {
                    $this->addFlash("error", "Erreur de saisie");
                }

                if ($form->isSubmitted() && $form->isValid()) {
                    $revue = $form->getData();
                    if ($candidatureOffre) $revue->setCandidatureOffre($candidatureOffre);

                    try {
                        $revue->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));
                    } catch (Exception $e) {
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($revue);
                    $entityManager->flush();

                    return $this->redirectToRoute('revue_par_offre', [
                        'offreDeTravailId' => $offreDeTravailId,
                        'revueId' => $revue->getId(),
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

        return $this->render('front_end/societe/offre_de_travail/revue/afficher_tout.html.twig', [
            'offreDeTravail' => $this->getDoctrine()->getRepository(OffreDeTravail::class)->find($offreDeTravailId),
            'societe' => null,
            'totalRevues' => count($revues),
            'revues' => $paginator->paginate(
                $revues,
                $request->query->getInt('page', 1), 6
            ),
            'form' => $formView,
            'revueId' => $revueId
        ]);
    }

    /**
     * @Route("societe/{societeId}", name="afficher_revue_par_societe")
     */
    public function afficherRevueParSociete($societeId, Request $request, PaginatorInterface $paginator): Response
    {
        $revues = $this->getDoctrine()->getRepository(Revue::class)->findBySociete($societeId);

        return $this->render('front_end/societe/offre_de_travail/revue/afficher_tout.html.twig', [
            'offreDeTravail' => null,
            'societe' => $this->getDoctrine()->getRepository(Societe::class)->find($societeId),
            'totalRevues' => count($revues),
            'revues' => $paginator->paginate(
                $revues,
                $request->query->getInt('page', 1), 6
            ),
            'form' => null,
            'revueId' => null
        ]);
    }

    /**
     * @Route("{candidatureOffreId}/ajouter")
     * @throws Exception
     */
    public function ajouterRevue(Request $request, $candidatureOffreId)
    {

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        $candidatureOffre = $this->getDoctrine()->getRepository(CandidatureOffre::class)->find($candidatureOffreId);

        $revue = new Revue();

        if (($user == $candidatureOffre->getCandidat()->getUser()) && ($candidatureOffre->getEtat() == "accepté")) {

            $form = $this->createForm(RevueType::class, $revue)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $revue = $form->getData();

                if ($candidatureOffre) $revue->setCandidatureOffre($candidatureOffre);
                $revue->setDateCreation(new DateTime('now', new DateTimeZone('Africa/Tunis')));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($revue);
                $entityManager->flush();

                return $this->redirectToRoute('revue_par_offre', [
                    'offreDeTravailId' => $candidatureOffre->getOffreDeTravail()->getId(),
                    'revueId' => $revue->getId(),
                ]);
            }

            return $this->render('front_end/societe/offre_de_travail/revue/manipuler.html.twig', [
                'candidatureOffre' => $candidatureOffre,
                'revue' => $revue,
                'form' => $form->createView(),
                'manipulation' => "Ajouter"
            ]);
        } else {
            throw new Error("Access denied");
        }
    }

    /**
     * @Route("{idRevue}/modifier")
     */
    public function modifierRevue(Request $request, $idRevue)
    {

        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $revue = $this->getDoctrine()->getRepository(Revue::class)->find($idRevue);
        $candidatureOffre = $revue->getCandidatureOffre();

        if ($user) {
            if ($user == $revue->getCandidatureOffre()->getCandidat()->getUser()) {

                $form = $this->createForm(RevueType::class, $revue)
                    ->add('submit', SubmitType::class)
                    ->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $revue = $form->getData();
                    if ($candidatureOffre) $revue->setCandidatureOffre($candidatureOffre);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($revue);
                    $entityManager->flush();

                    return $this->redirectToRoute('revue_par_offre', [
                        'offreDeTravailId' => $candidatureOffre->getOffreDeTravail()->getId(),
                        'revueId' => $revue->getId(),
                    ]);
                }

                return $this->render('front_end/societe/offre_de_travail/revue/manipuler.html.twig', [
                    'candidatureOffre' => $candidatureOffre,
                    'revue' => $revue,
                    'form' => $form->createView(),
                    'manipulation' => "Modifier"
                ]);
            } else {
                throw new Error("Vous ne pouvez pas modifier les revues des autres candidats !");
            }
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("{idRevue}/supprimer", name="supprimerRevue")
     */
    public function supprimerRevue($idRevue): Response
    {
        $manager = $this->getDoctrine()->getManager();
        $revue = $manager->getRepository(Revue::class)->find($idRevue);
        $manager->remove($revue);
        $manager->flush();

        $this->addFlash('success', 'Votre revue a été supprimé.');
        return $this->redirect('/revue/offre_de_travail/' . $revue->getCandidatureOffre()->getOffreDeTravail()->getId());
    }

    /**
     * @Route("recherche_par_societe", name="recherche_par_societe")
     */
    public function rechercheParSociete(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $valeurRecherche = $request->get('valeur-recherche');
        if ($valeurRecherche != null) {
            $societe = $this->getDoctrine()->getRepository(Societe::class)->findStartingWith($valeurRecherche);
            if ($societe) {
                $revues = $this->getDoctrine()->getRepository(Revue::class)->findBySociete($societe->getId());
            } else {
                return new Response(null);
            }
            if ($revues) {
                $i = 0;
                $jsonContent = null;
                foreach ($revues as $revue) {
                    $jsonContent[$i]['id'] = $revue->getId();
                    $jsonContent[$i]['idCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getId();
                    $jsonContent[$i]['nomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getNom();
                    $jsonContent[$i]['prenomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getPrenom();
                    $jsonContent[$i]['idPhotoCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getIdPhoto();
                    $jsonContent[$i]['idSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getId();
                    $jsonContent[$i]['nomSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
                    $jsonContent[$i]['idOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getId();
                    $jsonContent[$i]['nomOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getNom();
                    $jsonContent[$i]['salaire'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSalaire();
                    $jsonContent[$i]['nbEtoiles'] = $revue->getNbEtoiles();
                    $jsonContent[$i]['objet'] = $revue->getObjet();
                    $jsonContent[$i]['description'] = $revue->getDescription();
                    $jsonContent[$i]['dateCreation'] = $revue->getDateCreation()->format('H:i - d/M/Y');

                    if ($revue->getCandidatureOffre()->getCandidat()->getUser() == $user) {
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
     * @Route("recherche_par_nb_etoiles", name="recherche_par_nb_etoiles")
     */
    public function rechercheParNbEtoiles(Request $request): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        $societeId = (int)$request->get('societeId');
        $offreDeTravailId = (int)$request->get('offreDeTravailId');
        $nbEtoiles = (int)$request->get('valeur-recherche');

        $revues = null;

        if ($societeId) {
            $revues = $this->getDoctrine()->getRepository(Revue::class)->findBySocieteAndNbEtoiles(
                $societeId, $nbEtoiles
            );
        } elseif ($offreDeTravailId) {
            $revues = $this->getDoctrine()->getRepository(Revue::class)->findByOffreAndNbEtoiles(
                $offreDeTravailId, $nbEtoiles
            );
        } else {
            $revues = $this->getDoctrine()->getRepository(Revue::class)->findBy([
                'nbEtoiles' => $nbEtoiles
            ], [
                'dateCreation' => 'DESC'
            ]);
        }

        if ($nbEtoiles != null) {
            if ($revues) {
                $i = 0;
                $jsonContent = null;
                foreach ($revues as $revue) {
                    $jsonContent[$i]['id'] = $revue->getId();
                    $jsonContent[$i]['idCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getId();
                    $jsonContent[$i]['nomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getNom();
                    $jsonContent[$i]['prenomCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getPrenom();
                    $jsonContent[$i]['idPhotoCandidat'] = $revue->getCandidatureOffre()->getCandidat()->getIdPhoto();
                    $jsonContent[$i]['idSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getId();
                    $jsonContent[$i]['nomSociete'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSociete()->getNom();
                    $jsonContent[$i]['idOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getId();
                    $jsonContent[$i]['nomOffre'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getNom();
                    $jsonContent[$i]['salaire'] = $revue->getCandidatureOffre()->getOffreDeTravail()->getSalaire();
                    $jsonContent[$i]['nbEtoiles'] = $revue->getNbEtoiles();
                    $jsonContent[$i]['objet'] = $revue->getObjet();
                    $jsonContent[$i]['description'] = $revue->getDescription();
                    $jsonContent[$i]['dateCreation'] = $revue->getDateCreation()->format('H:i - d/M/Y');

                    if ($revue->getCandidatureOffre()->getCandidat()->getUser() == $user) {
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
}
