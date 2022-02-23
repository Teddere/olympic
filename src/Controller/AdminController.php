<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Menu;
use App\Entity\SousMenus;
use App\Entity\UsersMenus;
use App\Entity\Message;
use App\Entity\Notification;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class AdminController extends AbstractController
{
    public function __construct(SessionInterface $session,ManagerRegistry $doctrine){

        $this->usersession=$session;
        $this->getDoctrine=$doctrine;
    }
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response //Connexion avec l'application
    {
        // Chargement de la variable de session
        $u_session=$this->usersession->get('user',array());
        // Lorsque la session existe
        if(!empty($u_session)){
            return $this->render('admin/index.html.twig');
        }
        // Losque la session n'existe pas ou nouvelle connexion à l'application
        else{
            // Chargement des tables utilisées lors de la connexion à l'application
            $repository=$this->getDoctrine->getRepository(Users::class);
            $menu=$this->getDoctrine->getRepository(Menu::class);
            $sub_menu=$this->getDoctrine->getRepository(SousMenus::class);
            $user_menu=$this->getDoctrine->getRepository(UsersMenus::class);
            // Fin 
            $request = Request::createFromGlobals();
            // Verification du moyen d'envoi des données
            if($request->getMethod() === 'POST')
            {
                // Chargement du login et mot de passe
                    $login=$_POST['login'];
                    $pwd=$_POST['pwd'];
                // Fin 
                // Envoi de la requette dans la base de données afin de connaître l'existence de l'utlisateur
                    $user=$repository->findOneBy(['email'=>$login,'password'=>$pwd]);
                // Fin
                // Si l'utilisateur n'existe pas dans la base de données
                    if(empty($user)){
                        return $this->render('admin/login.html.twig',['msg'=>'L\'adresse email ou le mot de passe est incorrect ']);
                    }
                //FIn 
                // Lorsque l'untilisateur existe dans la base de données
                    // Recupération des données de l'utilisateur
                    $u_datas=array(
                        "id"=>$user->getId(),
                        "nom"=>$user->getNom(),
                        "prenom"=>$user->getPrenom(),
                        "email"=>$user->getEmail(),
                        "password"=>$user->getPassword(),
                        "avatar"=>$user->getAvatar(),
                        "job"=>$user->getJob(),
                        "status"=>$user->getStatus()
                    );
                    $dashbordMenu=array(); 
                    // Identification du menu de l'utilisateur
                    $listeMenu=$user_menu->findBy(['idUsers'=>$user->getId()],['idMenu'=>'asc']);
                    
                    // Récupération du menu de l'utilisateur
                    foreach($listeMenu as $element){
                        $tmp=array();
                        
                        $tmp['id']=$element->getIdMenu();
                        // fonction getMenus(table Menu, id du menu)
                        $tmp['name']=$this->getMenus($menu,$element->getIdMenu());
                        
                        
                        if(!empty($element->getIdMenu())){
                            // fonction sousMenus (table sousMenu, id du menu, sousMenus attachés à id du menu)
                            $tmp['sub_menu']=$this->sousMenus($sub_menu,$element->getIdMenu(),$element->getIdSousMenus());

                        }
                        
                        $dashbordMenu[]=$tmp;
                        
                    }
                   $this->usersession->set('user',$u_datas);
                    $this->usersession->set('menu',$dashbordMenu);  
                    
                    return $this->render('admin/index.html.twig');
                    
                }
                //Lorsque les données ne sont pas envoyées en post
                else{
                    return $this->render('admin/login.html.twig'); 
                }


                    
              
            }
    }
    // Récupération du menu 
    protected function getMenus($menu,$id){
        $str=$menu->find($id);            
        return $str;
        }
    // Récupération du Sous menu
    protected function sousMenus($sub_menu,$idMenu,$listSubMenu){
                
                
                $response=array();
                $tmp_sb=$sub_menu->findBy(['idMenu'=>$idMenu]);

                foreach($tmp_sb as $menu){
                    if($menu->getIdMenu()==$idMenu){
                        foreach(explode('#',$listSubMenu) as $sbMenu){

                            if($menu->getId()==$sbMenu){
                                $tmp=array(
                                    "id"=>$menu->getId(),
                                    "name"=>$menu->getName(),
                                    "link_name"=>$menu->getLinkName()
                                );
                                $response[]=$tmp;
                            }
                        }
                    }
                }
                return $response;
            }
    // Partie 2: Chargement des messages et des notifications

    /**
     * @Route("/loadAccount/{await}/{account_id}", name="loadAccount")
     */
    
    public function loadAccount($await,$account_id){
        
        if($await=="message"){
            return $this->loadMessage($account_id);
        }
        elseif($await=="notification"){
            return $this->loadNotification($account_id);
        }
    }
// Je dois revenir ici pour faire de lointure afin d'afficher le nom du expéditeur de message
    private function loadMessage($id){
        $msg=$this->getDoctrine->getRepository(Message::class);
        $listMsg=$msg->findBy(['idUser'=>$id]);
        
        $arr=array();
        if(!empty($listMsg)){
            foreach($listeMsg as $element){
                $arr=array(
                    "id"=>$element->getId(),
                    "id_account"=>$element->getIdUser(),
                    "content"=>$element->getContentMessage()
                );
                
            }
            return new JsonResponse($arr);
        }
        
      return new JsonResponse($arr['status']=1);  

    }
    private function loadNotification($id){
        $notif=$this->getDoctrine->getRepository(Notification::class);
        $listNotif=$notif->findBy(['idUser'=>$id]);

        $arr=array();
        if(!empty($listNotif)){
            foreach($listeMsg as $element){
                $arr=array(
                    "id"=>$element->getId(),
                    "id_account"=>$element->getIdUser(),
                    "content"=>$element->getContentMessage()
                );
                
            }
            return new JsonResponse($arr);
        }

        return new JsonResponse($arr['status']=1); 
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(){
        $this->usersession->remove('user');
        return $this->render('admin/login.html.twig',[
            'controller_name' => 'AdminController',
        ]);
    }
}
