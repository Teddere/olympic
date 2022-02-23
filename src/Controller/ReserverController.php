<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Customer;
use App\Entity\Reservation;
use App\Entity\ReservationValide;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ReserverController extends AbstractController
{
    public function  __construct(SessionInterface $session,ManagerRegistry $doctrine){
        $this->usersession=$session;
        $this->getDoctrine=$doctrine;
    }
    /**
     * @Route("Réservations/{await}", name="Réservations")
     */
    public function index($await): Response
    {
        if($await=='reserveList'){

            return $this->render('reserver/reserve.html.twig');
        } elseif($await=='validation'){
            return $this->render('reserver/validation.html.twig');
        }
        else {
            return $this->render('reserver/index.html.twig', [
                'controller_name' => 'ReserverController',
            ]);
        }
    }
//#####################Les chambres #######################################

    /**
     * @Route("getLoadListReserver/", name="getLoadListReserver")
     */
//Chargelent de toutes les chambres existentes
    public function loadListReserver(){
        $response=array();

        $repository=$this->getDoctrine->getRepository(Room::class);
        $list_room=$repository->findAll();

        foreach($list_room as $room){
            $tmp=array(
                "id"=>$room->getId(),
                "name"=>$room->getName(),
                "statut"=>$room->getStatut()
            );
            $response[]=$tmp;
        }
        return new JsonResponse($response);
    }
//Fin Chargelent de toutes les chambres existentes
    /**
     * @Route("getloadListOccupation/{status}", name="getloadListOccupation")
     */
//Chargelent de toutes les chambres Occupées, libres et en cours de réservation
    public function loadListOccupation($status){
        $response=array();
        $repository=$this->getDoctrine->getRepository(Room::class);
        $list_room=$repository->findBy(['statut'=>$status]);

        foreach($list_room as $room){
            $tmp=array(
                "id"=>$room->getId(),
                "name"=>$room->getName(),
                "status"=>$room->getStatut()
            );
            $response[]=$tmp;
        }
        
        return new JsonResponse($response);
    }
//Chargelent de toutes les chambres Occupées, libres et en cours de réservation
    /**
     * @Route("getloadElementRoom/{await}", name="getloadElementRoom")
     */
    public function loadElementRoom($await){
        $response=array();
        $repository=$this->getDoctrine->getRepository(Room::class);
        $list_room=$repository->findBy(['id'=>$await]);

        if(empty($list_room)){
            return new JsonResponse($response['status']=1);
        }else {
            foreach($list_room as $room){
                $tmp=array(
                    "id"=>$room->getId(),
                    "name"=>$room->getName(),
                    "status"=>$room->getStatut()
                );
                $response[]=$tmp;
            }
        }

        return new JsonResponse($response);
    }
//Fin Chargelent de toutes les chambres Occupées, libres et en cours de réservation
    /**
     * @Route("setElementRoom/", name="setElementRoom")
     */
//Insertion d'une nouvelle chambre
    public function setElementRoom(Request $request){
        $response=array();
        $entityManager=$this->getDoctrine->getManager();
        $dataForm=json_decode($request->getContent(),true);
        if(empty($dataForm['statut']) || $dataForm['statut']=='false'){
            $dataForm['statut']=0;
        }
        if($dataForm['id'] > 0){
            $room=$entityManager->getRepository(Room::class)->find($dataForm['id']);
            if(empty($room)){
                return new JsonResponse($response['msg']="Cette chambre n'existe pas ");
            }
        }
        elseif($dataForm['id'] == 0){
            $room= new Room();
        }
        else {
            return new JsonResponse($response['msg']="Cette chambre n'existe pas ");
        }
        if($dataForm['statut']==false){
            $dataForm['statut']=0;
        }else {
            $dataForm['statut']=1;
        }
         
        $room->setName($dataForm['nom']);
        $room->setStatut($dataForm['statut']);

        if($dataForm['id']==0){
            $entityManager->persist($room);
        }
        $entityManager->flush();


        $response['msg']=($dataForm['id'] > 0) ? 'Modification effectée avec succès !' : 'Création de nouvelle chambre reussie !';
        return new JsonResponse($response['msg']);
    }
//Fin Insertion d'une nouvelle chambre
    /**
     * @Route("setDeleteRoom/{await}", name="setDeleteRoom")
     */
//Suppression  d'une chambre
    public function setDeleteRoom($await){
            $response=array();
            $entityManager= $this->getDoctrine->getManager();
            $reserve=$entityManager->getRepository(Reservation::class)->findBy(['idRoom'=>$await]);
            
            if(!empty($reserve)){
                if($reserve[0]->getStatut()==2){
                    $response['msg']="Cette chambre est réservée et ne peut pas être supprimer";
                }else{
                    $response['msg']="Cette chambre est en cours de réservation et ne peut pas être supprimer";
                }
                $response['statut']="error";
                return new JsonResponse($response);
            }else{
                $room=$entityManager->getRepository(Room::class)->find($await);
                if(!empty($room)){
                    $entityManager->remove($room);
                    $entityManager->flush();
                    $response['statut']="success";
                    $response['msg']="Supprission effectée avec succès !";
                    return new JsonResponse($response);  
                }else{
                    $response['statut']="error";
                    $response['msg']="Oération non effectée !";
                    return new JsonResponse($response);
                }
            }
    }
//Fin Suppression  d'une chambre
//#####################Les chambres #######################################
//#####################Les Réservations #######################################
    /**
     * @Route("loadReservation/", name="loadReservation")
     */
//Chargement de toutes les réservations
    public function loadReservation(){
        $response=array();
        $repository=$this->getDoctrine->getRepository(Reservation::class);
        $liste_r=$repository->findAll();
        foreach($liste_r as $reserve){
            $tmp=array(
                "id"=>$reserve->getId(),
                "statut"=>$reserve->getStatut(),
                "name_room"=>$this->reserverRoom($reserve->getIdRoom()),
                "name_cl"=>$this->loadElementCustomers($reserve->getIdCustomer())
            );
            $tmp['date_ar']=$reserve->getArrivalDate()->format('d-m-Y');
            $tmp['date_re']=$reserve->getReverserDate()->format('d-m-Y');
            $response[]=$tmp;
        }
        return new JsonResponse($response);
    }
    //Chargements des clients ayant la réservation
    private function loadElementCustomers($id){
        $response=array();
        $respository = $this->getDoctrine->getRepository(Customer::class);
        $customer=$respository->findBy(['id'=>$id]);
        foreach($customer as $i){
            $response["id"]=$i->getId();
            $response["lname"]=$i->getNom();
            $response["fname"]=$i->getPrenom();
            
        }
        return $response;
    }
    //Chargements des Chambres ayant été réserveée
    private function reserverRoom($id){
        $response=array();
        $respository = $this->getDoctrine->getRepository(Room::class);
        $customer=$respository->findBy(['id'=>$id]); 
        foreach($customer as $i){
            $response["id"]=$i->getId();
            $response["name"]=$i->getName();
        }
        return $response; 
    }
//Fin Chargement de toutes les réservations
    /**
     * @Route("loadListCustomers/{await?}", name="loadListCustomers")
     */
//Chargement des tous les clients ayant la statut = 0 dans la modale
    public function loadListCustomers($await){
        $response=array();
        $repository=$this->getDoctrine->getRepository(Customer::class);
        $liste=$repository->findBy(['statut'=>$await]);
        
        foreach($liste as $element){
            $tmp=array(
                "id"=>$element->getId(),
                "nom"=>$element->getNom(),
                "prenom"=>$element->getPrenom()
            );
            $response[]=$tmp;
        }
        
        return new JsonResponse($response);
    }
//Fin Chargement des tous les clients ayant la statut = 0 dans la modale
    /**
     * @Route("sendReservation/", name="sendReservation")
     */
//Enregistrement des réservations
    public function sendReservation(Request $request){
        $response=array();
        $status=0;
        $entityManager=$this->getDoctrine->getManager();
        $dataForm=json_decode($request->getContent(),true);
        if(!empty($dataForm)){
        // Conversion de la Date vers le type DateTime
            $t=explode('-',$dataForm['dateR']);
            $date_r= new \DateTime();
            $date_n= new \DateTime();
            $date_r->setDate($t[0],$t[1],$t[2]);
            $date_n->createFromFormat('Y-m-d',$dataForm['dateR']);
        // Fin Conversion de la Date vers le type DateTime

        // Validation du client à la réservation de chambre
            if($dataForm['statut'] >=1 && $dataForm['statut']<=2){
                $customer=$entityManager->getRepository(Customer::class)->find($dataForm['idCustomer']);
                if(!empty($customer)){
                    $customer->setStatut($dataForm['statut']);
                    $entityManager->flush();
                }else{$status=0;}
            }else{$status=0;}
        //Fin Validation du client à la réservation de chambre

            // Validation de la réservation de la chambre réservée
                if($dataForm['idRoom'] > 0){
                     $room=$entityManager->getRepository(Room::class)->find($dataForm['idRoom']);
                     if(!empty($room)){
                        $room->setStatut($dataForm['statut']);
                        $entityManager->flush();
                     }else{$status=0;}
            //Fin Validation de la réservation de la chambre réservée

                // Validation de la réservation dans la base de données
                    $reserve= new Reservation();
                    $reserve->setIdCustomer($dataForm['idCustomer']);
                    $reserve->setIdRoom($dataForm['idRoom']);
                    $reserve->setArrivalDate($date_r);
                    $reserve->setReverserDate($date_n);
                    $reserve->setStatut($dataForm['statut']);
                    $entityManager->persist($reserve);
                    $entityManager->flush();
                    $status=1;
                }else{$status=0;}
            // Fin Validation de la réservation dans la base de données

            
        }
        // Réponse à renvoyer à la fin du script
        $response['statut']=($status > 0)?'success':'error';
        $response['msg']=($status > 0)?'Réservation ajoutée':'Echec de réservation';
        return new JsonResponse($response);
    }
//Fin Enregistrement des réservations
    /**
     * @Route("validatimSimple/", name="validatimSimple")
     */
//Validation du statut de la réservation
    public function validatimSimple(Request $request){
        $response=array();
        $entityManager=$this->getDoctrine->getManager();
        $dataForm=json_decode($request->getContent(),true);
        
        if($dataForm > 0){
            if($dataForm['status']==2){

                //Validation de la réservation  
                    $customer=$entityManager->getRepository(Customer::class)->find($dataForm['idCustomer']);
                    if($customer->getStatut()==2 || $customer->getStatut()==1){
                        $customer->setStatut($dataForm['status']);
                        $entityManager->flush();
                    }
                    $room=$entityManager->getRepository(Room::class)->find($dataForm['idRoom']);
                    if($room->getStatut()==2 || $room->getStatut()==1){
                        $customer->setStatut($dataForm['status']);
                        $entityManager->flush(); 
                    }
                    $reserve=$entityManager->getRepository(Reservation::class)->find($dataForm['id']);
                    
                    $reserve->setStatut($dataForm['status']);
                    $entityManager->flush();
                //FinValidation de la réservation 
            }else{
                //Validation refusée
                    //Initiation du client avec un statut 0
                        $customer=$entityManager->getRepository(Customer::class)->find($dataForm['idCustomer']);
                        if(!empty($customer)){
                            $customer->setStatut(0);
                            $entityManager->flush();
                        }
                    //Initiation du client avec un statut 0

                    //FIn Initiation de la chambre avec un statut 0
                        $room=$entityManager->getRepository(Room::class)->find($dataForm['idRoom']);
                        if(!empty($room)){
                            $room->setStatut(0);
                            $entityManager->flush(); 
                        }
                    //FIn Initiation de la chambre avec un statut 0

                    //Suppression de la réservation et Ajout dans la liste de confirmation 
                        $reserve=$entityManager->getRepository(Reservation::class)->find($dataForm['id']);
                        if(!empty($reserve)){
                        //Ajout dans la liste de confirmation 
                            $d=explode('-',$reserve->getArrivalDate()->format('Y-m-d'));
                        
                            $date_r= new \DateTime();
                            $date_n= new \DateTime();
                            $date_n->createFromFormat('Y-m-d','2020-12-24');
                            $date_r->setDate($d[0],$d[1],$d[2]);

                            $validation= new ReservationValide();
                            $validation->setIdReserve($dataForm['id']);
                            $validation->setIdCustomer($dataForm['idCustomer']);
                            $validation->setIdRoom($dataForm['idRoom']);
                            $validation->setIdRoom($dataForm['idRoom']);
                            $validation->setDateArrive($date_r);
                            $validation->setDateDepart($date_n);
                            $entityManager->persist($validation);
                            $entityManager->flush();
                        //Fin Ajout dans la liste de confirmation
                        
                        //Suppression de la réservation
                            $entityManager->remove($reserve);
                            $entityManager->flush();
                        //FIn Suppression de la réservation
                        }
                    //Suppression de la réservation et Ajout dans la liste de confirmation
                //Validation refusée
            }
            
        }else{
            $response['statut']='error';
            $response['msg']="Echec de l'opération";
            return new JsonResponse($response);
        }
        $response['statut']='success';
        $response['msg']=($dataForm['status']==2)?"Réservation ajoutée":"Réservation annulée avec succèes";
        
        return new JsonResponse($response);
    }
//Fin Validation du statut de la réservation
    /**
     * @Route("loadIdReserve/{id?}", name="loadIdReserve")
     */
//Chargement de la date d'arrive
    public function loadIdReserve($id){
        $repository=$this->getDoctrine->getRepository(Reservation::class)->find($id);
        $response=array(
            "date"=>$repository->getArrivalDate()->format('Y-m-d')
        );
        
        return new JsonResponse($response);
    }
//Fin Chargement de la date d'arrive
    /**
     * @Route("setIdReserve/", name="setIdReserve")
     */
//Modification de la date de réservation
    public function setIdReserve(Request $request){
        $response=array();
        $dataForm=json_decode($request->getContent(),true);
        $entityManager=$this->getDoctrine->getManager();
        
        if($dataForm > 0){
            $reserve=$entityManager->getRepository(Reservation::class)->find($dataForm['id']);
            
            if(!empty($reserve)){
                
                $tmp=explode('-',$dataForm['date_r']);
                $date_r= new \DateTime();
                $date_r->setDate($tmp[0],$tmp[1],$tmp[2]);

                $reserve->setArrivalDate($date_r);

                $entityManager->flush();
                $response['statut']='success';
                $response['msg']='Date d\'arrivée modifiée avec succès !';
                return new JsonResponse($response);
            }else{
                $response['statut']='error';
                $response['msg']='Echec de l\'opération !';
                return new JsonResponse($response);
            }
        }else{
            $response['statut']='error';
            $response['msg']='Echec de l\'opération !';
            return new JsonResponse($response);
        }


    }
//Fin Modification de la date de réservation
    /**
     * @Route("deleteReservation/{id}", name="deleteReservation")
     */
//Suppression de la réservation
    public function deleteReservation($id){
        $response=array();
        if($id > 0){
            $entityManager=$this->getDoctrine->getManager();
            $reserve=$entityManager->getRepository(Reservation::class)->find($id);
            if(!empty($reserve)){
                if(!empty($reserve->getIdCustomer())){
                //Initialisation du client
                    $customer=$entityManager->getRepository(Customer::class)->find($reserve->getIdCustomer());
                    if($customer->getStatut()==1){
                        $customer->setStatut(0);
                        $entityManager->flush();
                    }
                

                    elseif($customer->getStatut()==2){
                        $response['statut']='error'; 
                        $response['msg']='Le refus ne peut pas aboutir';
                        $response['detail']='La réservation a déjà été validée !';
                        return new JsonResponse($response);
                    }
                //Fin Initialisation du client
                //Initialisation de la chambre
                    if(!empty($reserve->getIdRoom())){
                        $room=$entityManager->getRepository(Room::class)->find($reserve->getIdRoom());
                        $room->setStatut(1);
                        $entityManager->flush();
                    }
                //Fin Initialisation de la chambre
                    $entityManager->remove($reserve);
                    $entityManager->flush();
                    $response['statut']='success';
                    $response['msg']='Suppression effectuée avec succès !';
    
                }
                return new JsonResponse($response);
            }else{
                $response['statut']='error';
                $response['msg']='Suppression non effectuée !';
                return new JsonResponse($response);
            }
        }else{
            $response['statut']='error';
            $response['msg']='Suppression non effectuée !';
            return new JsonResponse($response);
        }
    }
//Fin Suppression de la réservation
    /**
     * @Route("validationReservation/", name="validationReservation")
     */
//Chargement de réservation validée
    public function validationReservation(){
        $response=array();
        $repository=$this->getDoctrine->getRepository(ReservationValide::class);
        $reservationValide=$repository->findAll();

        foreach($reservationValide as $valide){
            $tmp=array(
                "id"=>$valide->getId(),
                "idReser"=>$valide->getIdReserve(),
                "idCustomer"=>$this->loadElementCustomers($valide->getIdCustomer()),
                "idRoom"=>$this->reserverRoom($valide->getIdRoom()),
                "date_d"=>$valide->getDateDepart()->format('Y-m-d'),
                "date_r"=>$valide->getDateArrive()->format('Y-m-d')
            );
            $response[]=$tmp;
        }
        return new JsonResponse($response);
    }
//#####################Fin Les Réservations #######################################
//#####################Liste de confirmation #######################################
//Chargement de réservation validée
    /**
     * @Route("confirmReserve/", name="confirmReserve")
     */
//Confirmation de statut de validation
    public function confirmReserve(Request $request){
        $response=array();
        $entityManager=$this->getDoctrine->getManager();
        $dataForm=json_decode($request->getContent(),true);
        
        if($dataForm > 0){
        //Initialisation du client
            $customer=$entityManager->getRepository(Reservation::class)->find($dataForm['idCustomer']);
            if(!empty($customer)){
                $customer->setStatut(0);
                $entityManager->flush();
            }
        //Fin Initialisation du client

        //Initialisation de la chambre
            $room=$entityManager->getRepository(Reservation::class)->find($dataForm['idCustomer']);
            if(!empty($room)){
                $room->setStatut(0);
                $entityManager->flush();
            }
        //Fin Initialisation de la chambre
            $reserve=$entityManager->getRepository(Reservation::class)->find($dataForm['id']);
            if(!empty($reserve)){
                $d=explode('-',$reserve->getArrivalDate()->format('Y-m-d'));
                //Conversion de la date         
                    $date_r= new \DateTime();
                    $date_n= new \DateTime();
                    $date_n->createFromFormat('Y-m-d','2020-12-24');
                    $date_r->setDate($d[0],$d[1],$d[2]);
                //Fin Conversion de la date 

                //Insertion de la confirmation de réservations
                    $validation= new ReservationValide();
                    $validation->setIdReserve($dataForm['id']);
                    $validation->setIdCustomer($dataForm['idCustomer']);
                    $validation->setIdRoom($dataForm['idRoom']);
                    $validation->setDateArrive($date_r);
                    $validation->setDateDepart($date_n);
                    $validation->setStatut(1);
                    $entityManager->persist($validation);
                    $entityManager->flush();
                //fin Insertion de la confirmation de réservations

                //Suppression de la réservation
                    $entityManager->remove($reserve);
                    $entityManager->flush();
                //Fin Suppression de la réservation
                $response['statut']="success";
                $response['msg']="Séjours validé avec succès !";
            }else{
                $response['statut']="error";
                $response['msg']="Opération non reussie !";
            }
        }else{
            $response['statut']="error";
            $response['msg']="Opération non reussie !";
        }
        return new JsonResponse($response);
    }
//Confirmation de statut de validation
//#####################Fin Liste de confirmation #######################################
}
