<?php

namespace App\Controller;
use App\Entity\Users;
use App\Entity\UsersMenus;
use App\Entity\Menu;
use App\Entity\SousMenus;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccountController extends AbstractController
{
    public function __construct(SessionInterface $session,ManagerRegistry $doctrine){

        $this->usersession=$session;
        $this->getDoctrine=$doctrine;
    }
    /**
     * @Route("Administration/{await}", name="account")
     */
    public function index($await): Response
    {
        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
    /**
     * @Route("listAccount", name="listAccount")
     */
    public function listAccount(Request $request){
        $repository = $this->getDoctrine->getRepository(Users::class);
        $list_account=$repository->findAll();
        $arr=array();
        
        foreach($list_account as $element){
            $tmp=array(
                "id"    =>$element->getId(),
                "fname" =>$element->getNom(),
                "lname" =>$element->getPrenom(),
                "email" =>$element->getEmail(),
                "avatar"=>$element->getAvatar(),
                "job"   =>$element->getJob(),
                "status"=>$element->getStatus()
            );
            $arr[]=$tmp;
        }
        return new JsonResponse($arr);
    }
    /**
     * @Route("loadAcount/{id}", name="loadAcount")
     */
    public function loadAcount($id){
        if($id!=0){

            $arr=array();
            $repository = $this->getDoctrine->getRepository(Users::class);
            $account=$repository->find(['id'=>$id]);
            
            $arr=array(
                "id"    =>$account->getId(),
                "lname" =>$account->getNom(),
                "fname" =>$account->getPrenom(),
                "email" =>$account->getEmail(),
                "avatar"=>$account->getAvatar(),
                "status"=>$account->getStatus(),
                "job"   =>$account->getJob()
            );
            
            return new JsonResponse($arr);
        }else {
            return new JsonResponse($arr=0);
        }
    }
    /**
     * @Route("addAndModif/", name="addAndModif")
     */
    public function addAndModif(Request $request){
        $imgName='abcdefg';
        $arr=array();
        $entityManager=$this->getDoctrine->getManager();
        $data_from=json_decode($request->getContent(),true);

        if(!empty($data_from['data_image'])){
                foreach($data_from['data_image'] as $avatar){
                $exploded=explode(',',$avatar);
                $decoded=base64_decode($exploded[1]);
                if(str_contains($exploded[0],'jpeg')){
                    $extension='jpg';
                }else {
                    $extension='png';
                }
                
                $fileName=str_shuffle($imgName).'.'.$extension;
                $path=$this->getParameter('images_directory').'/'.$fileName;
                
                file_put_contents($path,$decoded);
            }
        }

        if($data_from['data_id']!=0){
            $account=$entityManager->getRepository(Users::class)->find($data_from['data_id']);
            if(empty($account)){
                return new JsonResponse($arr['msg']='Ce compte n\'existe pas !'); 
            }

        }else{
            $account= new Users();
        }
            $tmp_pwd="abcdefghijiklmnopqrstuvwxyz";
            if(!empty($data_from['data_image'])){
                $account->setAvatar($fileName);
            }
            $account->setNom($data_from['data_lname']);
            $account->setPrenom($data_from['data_fname']);
            $account->setEmail($data_from['data_email']);
            $account->setStatus($data_from['data_status']);
            $account->setJob($data_from['data_job']);
            $account->setPassword(str_shuffle($tmp_pwd));
            
        
        if($data_from['data_id']==0){
            $entityManager->persist($account);
        }
            $entityManager->flush();
        $arr['status']=1;
        $arr['msg']=($data_from['data_id'] > 0) ? 'Modification effectuée avec succès' : 'Creation de compte reussie';
        
        return new JsonResponse($arr); 
    }
    /**
     * @Route("deleteAccount/{id}", name="deleteAccount")
     */
    public function deleteAccount($id,Request $request):Response
    {
        $response=array();
        $u_session=$this->usersession->get('user',array());
        if($id!=0){
            $entityManager=$this->getDoctrine->getManager();
            $account=$entityManager->getRepository(Users::class)->find($id);

            $entityManager->remove($account);
            $entityManager->flush();
            $response['statut']="success";
            $response['msg']="Compte supprimé avec succès !";
            return new JsonResponse($response);
        }
        $response['statut']="error";
        $response['msg']="Suppression non effectuée !";
        return new JsonResponse($response);
    }
    /**
     * @Route("getloadMenu/{id_account?}", name="getloadMenu")
     */
    public function getloadMenu($id_account=null){
        //Recupérer les menus de l'utilisateur
        $arr=array();
        $list_menu=(!is_null($id_account))?$this->loadMenuAccount($id_account):$list_menu=array();
        $repository=$this->getDoctrine->getRepository(Menu::class);
        $table=$repository->findAll();
        foreach($table as $element){
            $tmp=array(
                "id"=>$element->getId(),
                "name"=>$element->getName(),
            );
            
            foreach($element->getMySousMenus() as $sb_menu){
                if($element->getId()==$sb_menu->getIdMenu()){
                    $tmp["sous_menu"][]=array(
                        "id"=>$sb_menu->getId(),
                        "name"=>$sb_menu->getName(),
                        "is_checked"=>$this->checkMenu($list_menu,$element->getId(),$sb_menu->getId())
                    );

                }

            }
            
            $arr[]=$tmp;
        }
        
        
        return new JsonResponse($arr);
    }
    // Cocher les menus que l'utlisateur a droit
    private function checkMenu($accountMenu,$id_menu,$id_sbMenu){
        foreach($accountMenu as $menu){
            
            if($menu['id']==$id_menu){
                return 1;
            }

        }
        return 0;

    }
    private function loadMenuAccount($id_account){
        $repository=$this->getDoctrine->getRepository(UsersMenus::class);
        $list_menu=$repository->findBy(['idUsers'=>$id_account]);
        $arr=array();
        foreach($list_menu as $index){
            $arr[]=array(
                "id"=>$index->getIdMenu(),
                "sous_menu"=>explode('#',$index->getIdSousMenus())
            );
        }
        return $arr;
    }
    /**
     * @Route("loadAccountMenu/{id}", name="loadAccountMenu")
     */
    public function loadAccountMenu($id){
        $listAccoutMenu=$this->loadMenuAccount($id);
        $arr=array();
        foreach($listAccoutMenu as $menu){
            foreach($menu['sous_menu'] as $sm){
                $str=$menu['id'].'_'.$sm;
                $arr[]=$str;
            }
        }
        return new JsonResponse($arr);
    }
    /**
     * @Route("validadtionDroit/", name="validadtionDroit")
     */
    public function validadtion(Request $request){
        $response=array();
        $tmp_menu=array();
        $arr=array();
        $data_from=json_decode($request->getContent(),true);

        if(!empty($data_from['id_account'])){
            foreach($data_from['validate'] as $menu){
                $element=explode('_',$menu);
                $tmp_menu[]=$element[0];
            }
            $tmp_menu=array_unique($tmp_menu);

            foreach($data_from['validate'] as $menu){
                $element    =explode('_',$menu);
                $tmp_menu[] =$element[0];

                if(in_array($element[0],$tmp_menu)){

                    if(!isset($arr[$element[0]])) {
                        $values = null;
                    } else {
                        $values = $arr[$element[0]];
                    }

                    if(is_null($values)){
                        $values = "{$element[1]}";
                        $arr[$element[0]] = $values;
                    }else {
                        $values .= "#{$element[1]}"; 
                        $arr[$element[0]] = $values;
                    }
                }
            }
        }
        if(count($arr) > 0){
            $entityManager=$this->getDoctrine->getManager();
            $userMenu=$entityManager->getRepository(UsersMenus::class)->findBy(['idUsers'=>$data_from['id_account']]);
            
            if(!empty($userMenu)){
                foreach($userMenu as $menuUser){
                    $entityManager->remove($menuUser);
                    $entityManager->flush();
                }
            }
            
            foreach($arr as $menu=>$sb_menu){
                    $userMenu= new UsersMenus();
                    $userMenu->setIdUsers($data_from['id_account']);
                    $userMenu->setIdMenu($menu);
                    $userMenu->setIdSousMenus($sb_menu);
                    $entityManager->persist($userMenu);
                    $entityManager->flush();
                }
            
            
            $response['status']=1;
            $response['msg']="Modification effectuée avec succès !";
        }
        
        return new JsonResponse($response);
    }
}
