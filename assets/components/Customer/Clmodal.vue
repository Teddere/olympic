<template>
    <div class="modall-backdrop">
        <transition name="fade">
            <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
                <div class="modall">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <header class="modal-header" id="modallTitle">
                                <slot name="header" class="modal-title" v-if="id_customer==0">
                                    Nouveau compte client
                                </slot>
                                <slot name="header" class="modal-title" v-else>
                                    Modification compte client
                                </slot>
                                <button type="button" 
                                    class="btn-close" 
                                    data-bs-dismiss="modall" aria-label="Close"
                                    @click="closeModal()"
                                >
                                </button>
                            </header>
                            <section class="modal-body m-2">
                                <form class="mb-0">
                                    <div class="input-group m-2">
                                        <select  class="form-select" v-model="genre" v-if="id_customer==0">
                                            <option value="M"  selected>Homme</option>
                                            <option value="F">Femme</option>
                                        </select>
                                        <select  class="form-select" v-else>
                                            <option v-if="genre=='M'" value="M" disabled selected>Homme</option>
                                            <option v-else disabled value="F" selected>Femme</option>
                                        </select>
                                    </div>
                                    <div class="input-group m-2">
                                        <input type="text" class="form-control" placeholder="Veuillez entrer le nom du client" v-model="lname" />
                                    </div>
                                    <div class="input-group m-2">
                                        <input type="text" class="form-control" placeholder="Veuillez entrer le prénom du client" v-model="fname" />
                                    </div>
                                    <div class="input-group m-2">
                                        <input type="email" class="form-control" placeholder="Veuillez entrer l'adresse email du client" v-model="email" />
                                    </div>
                                    <div class="input-group m-2">
                                        <input type="text" class="form-control" placeholder="Veuillez entrer le pays du client" v-model="contry" />
                                    </div>
                                    <div class="input-group m-2">
                                        <input  type="file"  class="form-control" @change="loadAvatar" />
                                    </div>
                                    <div class="modal-footer m-1">
                                        <button type="button" class="btn btn-primary" @click="addCustomers()" v-if="id_customer==0">Ajouter</button>
                                        <button type="button" class="btn btn-primary" @click="addCustomers()" v-else>Modifier</button>
                                        <button type="button" class="btn btn-secondary" @click="closeModal()">Annuler</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<script>
import EventBus from "../eventBus";
import Swal from "sweetalert2";
export default {
    data(){
        return{
            id_customer:0,
            lname:'',
            fname:'',
            email:'',
            genre:'M',
            contry:'',
            avatar:''
            }
    },
    mounted(){
        EventBus.$on('idCustomers',(id)=>{
            this.id_customer=id;
            this.loadCustomers();
        })
    },
    methods:{
        async loadCustomers(){
            if(this.id_customer > 0){
                const response = await fetch('/loadCustomers/'+this.id_customer);
                const data = await response.json();
                this.lname=data.lname;
                this.fname=data.fname;
                this.email=data.email;
                this.genre=data.genre;
                this.contry=data.contry;
            }else{
              this.id_customer=0;  
            }
        },
        async addCustomers(){
            const dataFrom={
                id:this.id_customer,
                nom:this.lname,
                prenom:this.fname,
                civil:this.genre,
                email:this.email,
                pays:this.contry,
                avatar:this.avatar
            }
            const dataHeaders={
                method:"POST",
                headers:{"content-Type":"application/json"},
                body: JSON.stringify(dataFrom)
            }
            const response = await fetch('/addCustomers/',dataHeaders);
            const data = await response.json();
            EventBus.$emit("reloadCustomers");
            this.closeModal();
            Swal.fire({
                icon:data.statut,
                text:data.msg,
                confirmButtonColor:'#3b7ddd'
            });
        },
        loadAvatar(event){
            let file = new FileReader();
            file.readAsDataURL(event.target.files[0]);
            file.onload=(e)=>{
                this.avatar=e.target.result;
            }
        },
        closeModal(){
            this.id_customer=0;
            this.lname='';
            this.fname='';
            this.email='';
            this.genre='M';
            this.contry='';
            this.status=false;
            this.avatar='';
            this.$emit('close');
        }
    },
    components:{
        EventBus,Swal
    }
}
</script>