<?php

namespace App\Controller\back_end_societe;

use App\Entity\Mission;
use App\Entity\Question;
use App\Entity\User;
use App\Form\MissionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("espace_societe/")
 */
class MissionController extends AbstractController
{
    /**
     * @Route("mission")
     */
    public function afficherToutMission(Request $request): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $request->getSession()->get(Security::LAST_USERNAME)
        ]);
        return $this->render('back_end_societe/societe/mission/afficher_tout.html.twig', [
            'missions' => $this->getDoctrine()->getRepository(Mission::class)->findBy([
                'societe' => $user->getSociete()
            ])
        ]);
    }

    /**
     * @Route("mission/{idMission}/afficher")
     */
    public function afficherMission($idMission): Response
    {
        return $this->render('back_end_societe/societe/mission/afficher.html.twig', [
            'missions' => $this->getDoctrine()->getRepository(Mission::class)->find($idMission)
        ]);
    }

    /**
     * @Route("mission/recherche")
     * @throws ExceptionInterface
     */
    public function rechercheMission(Request $request, NormalizerInterface $normalizer): Response
    {
        $recherche = $request->get("valeurRecherche");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->findOneByMissionName($recherche);

        $jsonContent = $normalizer->normalize($mission, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    /**
     * @Route("mission/ajouter")
     */
    public function ajouterMission(Request $request): Response
    {
        $mission = new Mission();
        return $this->manipulerMission($request, 'Ajouter', $mission);
    }

    /**
     * @Route("mission/{idMission}/modifier")
     */
    public function modifierMission(Request $request, $idMission): Response
    {
        return $this->manipulerMission($request, 'Modifier',
            $this->getDoctrine()->getRepository(Mission::class)->find($idMission));
    }

    public function manipulerMission(Request $request, $manipulation, $mission): Response
    {
        $email = $request->getSession()->get(Security::LAST_USERNAME);
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $form = $this->createForm(MissionType::class, $mission)
                ->add('submit', SubmitType::class)
                ->handleRequest($request);

            if ($form->isSubmitted()) {

                $counter = (int)$request->get('counter');

                $mission = $form->getData();
                $mission->setSociete($user->getSociete());

                foreach ($mission->getQuestion() as $question) {
                    $mission->removeQuestion($question);
                }

                for ($i = 1; $i < $counter + 1; $i++) {
                    $questionsText = $request->get('question-' . $i);
                    $reponsesText = $request->get('reponse-' . $i);

                    $question = new Question();
                    $question->setDescription($questionsText);
                    $question->setReponse($reponsesText);
                    $mission->addQuestion($question);
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($mission);
                $entityManager->flush();

                return $this->redirect('/back_end_societe/mission');
            }

            return $this->render('back_end_societe/societe/mission/manipuler.html.twig', [
                'mission' => $mission,
                'form' => $form->createView(),
                'manipulation' => $manipulation,
            ]);
        } else {
            return $this->redirect('/connexion');
        }
    }

    /**
     * @Route("mission/{idMission}/supprimer")
     */
    public function supprimerMission($idMission): Response
    {
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($idMission);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($mission);
        $entityManager->flush();

        return $this->redirect('/back_end_societe/mission');
    }
}
