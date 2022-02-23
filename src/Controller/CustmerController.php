<?php

namespace App\Controller;
use App\Entity\Customer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CustmerController extends AbstractController
{
    public function __construct(SessionInterface $session,ManagerRegistry $doctrine){
        $this->getDoctrine=$doctrine;
        $this->usersession=$session;
    }
    /**
     * @Route("Client/{await}", name="Client")
     */
    public function index($await): Response
    {
        return $this->render('custmer/index.html.twig');
    }
    /**
     * @Route("loadCustomers/{id?}", name="loadCustomers")
     */
    public function loadCustomers($id=null){
        $response=array();
        $repository=$this->getDoctrine->getRepository(Customer::class);
        $list_customers=$repository->findAll();
        foreach($list_customers as $element){
            if(is_null($id)){
                $tmp=array(
                    "id"=>$element->getId(),
                    "lname"=>$element->getNom(),
                    "fname"=>$element->getPrenom(),
                    "genre"=>$element->getCivil(),
                    "contry"=>$element->getPays(),
                    "email"=>$element->getEmail(),
                    "avatar"=>$element->getAvatar(),
                    "status"=>$element->getStatut()
                );
                $response[]=$tmp;
            }else {
                if($element->getId()==$id){
                    $response=array(
                    "id"=>$element->getId(),
                    "lname"=>$element->getNom(),
                    "fname"=>$element->getPrenom(),
                    "genre"=>$element->getCivil(),
                    "contry"=>$element->getPays(),
                    "email"=>$element->getEmail(),
                    "avatar"=>$element->getAvatar(),
                    "statut"=>$element->getStatut()
                    );
                    return new JsonResponse($response); 
                }
            }
        }


        return new JsonResponse($response);
    }
    /**
     * @Route("addCustomers/", name="loadIdCustomers")
     */
    public function loadIdCustomers(Request $request){
        $response=array();
        $dataForm=json_decode($request->getContent(),true);
        if(!empty($dataForm['avatar'])){

            $imgName="abcdefgijklmnopq";
            $exploded=explode(',',$dataForm['avatar']);
            $decoded=base64_decode($exploded[1]);
            if(str_contains($exploded[0],'jpeg')){
                $extension='jpg';
            }else {
                $extension='png';
            }
            $fileName=str_shuffle($imgName).'.'.$extension;
            $path=$this->getParameter('image_customes').'/'.$fileName;
            file_put_contents($path,$decoded);
        }

        $entityManager=$this->getDoctrine->getManager();
        if($dataForm['id'] > 0){
            $customer=$entityManager->getRepository(Customer::class)->find($dataForm['id']);
            if(empty($customer)){
                $response['statut']='error';
                $response['msg']='Le compte n\'exist pas';
                return new JsonResponse($response);
            }
        }elseif($dataForm['id']==0){
            $customer= new Customer();
        }else {
            $response['statut']='error';
            $response['msg']='Ce compte n\'est pas identifié !';
            return new JsonResponse($response);
        }
            if(!empty($dataForm['avatar'])){
                $customer->setAvatar($fileName);
            }
            $customer->setNom($dataForm['nom']);
            $customer->setPrenom($dataForm['prenom']);
            $customer->setCivil($dataForm['civil']);
            $customer->setEmail($dataForm['email']);
            $customer->setPays($dataForm['pays']);
            $customer->setStatut(0);

            if($dataForm['id']==0){
                $customer->setPassword($dataForm['nom']);
                $entityManager->persist($customer);
            }
            $entityManager->flush();

            $response['statut']='success';
            $response['msg']=($dataForm['id'] !=0)?'Modification effectuée avec succès !':'Compte client ajouté avec succès !';
        return new JsonResponse($response);
    }
    /**
     * @Route("deleteCustomers/{id}", name="deleteCustomers")
     */
    public function deleteCustomers($id){
        $response=array();
        if($id <= 0){
            $response['statut']='error';
            $response['msg']='Ce compte n\'existe pas';
            return new JsonResponse($response);
        }else {
            $entityManager=$this->getDoctrine->getManager();
            $customer=$entityManager->getRepository(Customer::class)->find($id);
            if(!empty($Customer)){
                $response['statut']='error';
                $response['msg']='Ce compte n\'existe pas';
                return new JsonResponse($response);   
            }
            $entityManager->remove($customer);
            $entityManager->flush();
            $response['statut']='success';
            $response['msg']='Compte supprimé avec succès !';
            return new JsonResponse($response);
        }
    }
}
