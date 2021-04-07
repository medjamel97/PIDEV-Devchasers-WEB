<?php

namespace App\Controller\back_end;

use App\Entity\Candidat;
use App\Entity\CandidatureMission;
use App\Entity\Mission;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Form\FormType;
use App\Form\QuestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Routing\Annotation\Route;


class MissionController extends Controller
{
    /**
     * @Route("missionsearch", name="missionsearch")
     */
    public function missionsearch(Request $request, NormalizerInterface $normalizer)
    {
        $recherche = $request->get("searchValue");
        //$mission=$this->getDoctrine()->getRepository(Mission::class)->findBy(['mission_name'=>$recherche]);
        $mission = $this->getDoctrine()->getRepository(Mission::class)->findOneByMissionName($recherche);

        // $maValeur = $request->get("tab");
        // $tab=explode (',' ,$maValeur );
        // $em=$this->getDoctrine()->getManager();
        //   for($i=0; $i<count($tab);$i++)
        //   {if($tab[$i]!="undefined")
        //    { $Questionnaire= new Questionnaire();
        //     $Questionnaire->setDescription($tab[$i])->setMission($mission);
        //    $em->persist($Questionnaire);
        //    $em->flush();}
        //   }
        //    return $this->redirectToRoute('mission');

        $jsonContent = $normalizer->normalize($mission, 'json', ['groups' => 'post:read',]);
        $retour = json_encode($jsonContent);
        return new Response($retour);
    }

    /**
     * @Route("back_end/ajouterMission", name="AjouterMission")
     */
    public function AddMission(Request $request): Response
    {
        $mission = new Mission();
        $form = $this->createForm(FormType::class, $mission);
        $form->add('Ajouter', SubmitType::class, array('label' => 'Ajouter / Donner vos questions  >'));
        $form->handleRequest($request);
        // && $form->isValid()
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mission);
            $em->flush();
            $id = $mission->getId();
            return $this->redirectToRoute('addQuest', array('name' => $id));
        }
        return $this->render('/back_end/mission/essai.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("addQuest/name={name}", name="addQuest")
     */
    public function AddQuestMission(Request $request, $name): Response
    {
        $Questionnaire = new Questionnaire();
        $form = $this->createForm(QuestType::class, $Questionnaire);
        // $form->add('Ajouter',SubmitType::class, array('label' => 'Ajouter votre questionnaire >'));
        $maValeur = $request->request->get("valeurArecuperer", "valeur par défaut si le champ n'existe pas");
        // $form->handleRequest($request);
        // if($form->isSubmitted()&& $form->isValid())
        // {
        //    $em=$this->getDoctrine()->getManager();
        //    $em->persist($Questionnaire);
        //    $em->flush();
        //   //  return $this->redirectToRoute('/questMission/',['id'=>$mission.id]);
        // }
        return $this->render('/back_end/mission/addQuestMission.html.twig', [
            'form' => $form->createView(),
            'id2' => $name,
        ]);

    }

    /**
     * @Route("addQuest/ajouterchamp/pp", name="ajouterchamp")
     */
    public function ajouterchamp(Request $request): Response
    {
        return new Response(null);

    }

    /**
     * @Route("back_end/addQuest/ajoutationQuestionnaire", name="ajoutationQuestionnaire")
     */
    public function ajouterQuestionnaire(Request $request, NormalizerInterface $normalizer): Response
    {
        $id = $request->get("id");
        $mission = $this->getDoctrine()->getRepository(Mission::class)->find($id);

        $maValeur = $request->get("tab");
        $tab = explode(',', $maValeur);
        $em = $this->getDoctrine()->getManager();
        for ($i = 0; $i < count($tab); $i++) {
            if ($tab[$i] != "undefined") {
                $Questionnaire = new Questionnaire();
                $Questionnaire->setDescription($tab[$i])->setMission($mission);
                $em->persist($Questionnaire);
                $em->flush();
            }
        }
        return New Response(null);
        //
        // $jsonContent = $normalizer->normalize($tab, 'json',['groups' => 'post:read',]);
        // $retour = json_encode($jsonContent);
        // return new Response($retour);
    }

    /**
     * @Route("back_end/questMission/condidature/i", name="condidature")
     */
    public function Addcondidature(Request $request, NormalizerInterface $normalizer): Response
    {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $question = $em->getRepository(Questionnaire::class)->findBy(['Mission' => $id]);
        $num = $request->get("num");
        $maValeur = $request->get("ch");
        $tab = explode(',', $maValeur);
        // $em=$this->getDoctrine()->getManager();
//replir la table question
        for ($i = 0; $i < count($tab); $i++) {
            if ($tab[$i] != "undefined") {
                $Questionnaire = new Question();
                $Questionnaire->setDescription($tab[$i])->setQuestionnaire($question[$i])->setNumReponse($num);
                $em->persist($Questionnaire);
                $em->flush();
            }
        }
//remplir la table condiature
        $condidat = $em->getRepository(Candidat::class)->find(1);
        $mission = $em->getRepository(Mission::class)->find($id);
        $condidature = new CandidatureMission();
        $condidature->setCandidat($condidat)->setMission($mission)->setNumreponse($num);
        $em->persist($condidature);
        $em->flush();
        return $this->redirectToRoute('mission');
        // $jsonContent = $normalizer->normalize($maValeur, 'json',['groups' => 'post:read',]);
        // $retour = json_encode($jsonContent);
        // return new Response($retour);
    }

    /**
     * @Route("societeMission/{id}", name="societeMission")
     */
    public function societeMission(Request $request, $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $mission = $em->getRepository(Mission::class)->findBy(['societe' => $id]);
        return $this->render('/back_end/mission/societeMission.html.twig', [
            'missions' => $mission,
        ]);
    }

    /**
     * @Route("deleteMission/{id}", name="deleteMission")
     */
    public function deleteMission($id)
    {
        $em = $this->getDoctrine()->getManager();
        $classe = $em->getRepository(Mission::class)->find($id);
        $em->remove($classe);
        $em->flush();
        return $this->redirectToRoute("mission");
    }

    /**
     * @Route("updateMission/{id}", name="modifierMission")
     */
    public function modifierMission(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $classroom = $em->getRepository(Mission::class)->find($id);
        $form = $this->createForm(FormType::class, $classroom);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('mission');
        }
        return $this->render('/back_end/mission/updateMission.html.twig', [
            "form-title" => "Modifier une Mission",
            "form" => $form->createView(),
        ]);
    }
}