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
    public function index(): Response
    {
        $u_session=$this->usersession->get('user',array());

        if(!empty($u_session)){
            return $this->render('admin/index.html.twig');
        }else{
            $repository=$this->getDoctrine->getRepository(Users::class);
            $menu=$this->getDoctrine->getRepository(Menu::class);
            $sub_menu=$this->getDoctrine->getRepository(SousMenus::class);
            $user_menu=$this->getDoctrine->getRepository(UsersMenus::class);

            $request = Request::createFromGlobals();
            
            if($request->getMethod() === 'POST')
            {

                    $login=$_POST['login'];
                    $pwd=$_POST['pwd'];

                    $user=$repository->findOneBy(['email'=>$login,'password'=>$pwd]);

                    if(empty($user)){
                        
                        return $this->render('admin/login.html.twig',['msg'=>'L\'adresse email ou le mot de passe est incorrect ']);
                    }
                    
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
                    
                    $listeMenu=$user_menu->findBy(['idUsers'=>$user->getId()]);
                    
                    
                    
                    foreach($listeMenu as $element){
                        $tmp=array();
                        
                        $tmp['id']=$element->getIdMenu();
                        $tmp['name']=$this->getMenus($menu,$element->getIdMenu());
                        
                        
                        if(!empty($element->getIdMenu())){
                            $tmp['sub_menu']=$this->sousMenus($sub_menu,$element->getIdMenu());

                        }

                        $dashbordMenu[]=$tmp;
                    }
                    
                   $this->usersession->set('user',$u_datas);
                    $this->usersession->set('menu',$dashbordMenu);   
                    
                    return $this->render('admin/index.html.twig');
                    
                }else{
                    return $this->render('admin/login.html.twig'); 
                }


                    
              
            }
    }
            
    protected function getMenus($menu,$id){
        $str=$menu->find($id);
                
        return $str;
        }

    protected function sousMenus($sub_menu,$id){
                $arr= array();
               
                foreach(explode('#',$id) as $t){
                    $tmp_sb=$sub_menu->findBy(['idMenu'=>$t]);
                    
                    foreach($tmp_sb as $i)
                    $arr[]=array(
                        "id"=>$i->getId(),
                        "name"=>$i->getName(),
                        "link_name"=>$i->getLinkName()
                    );
                    
                }
                return $arr;
            }

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
