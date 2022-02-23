<template>
    <div class="modall-backdrop">
    <transition name="fade">
        <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
            <div class="modall">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <header class="modal-header" id="modallTitle">
                            <slot name="header" class="modal-title">Nouvelle Réseervation</slot>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"></button>
                        </header>
                        <section class="modal-body m-2">
                            <form class="mb-0">
                                <div class="input-group m-2">
                                    <select  class="form-select" v-model="id_room">
                                        <option :value="0" selected disabled>Veuillez selectionner une chambre</option>
                                        <option v-for="room in list_room" :key="room.id" :value="room.id">{{room.name}}</option>
                                    </select>
                                </div>
                                <div class="input-group m-2">
                                    <select  class="form-select" v-model="id_customer">
                                        <option :value="0" selected disabled>Veuillez selectinner le client concerné</option>
                                        <option v-for="customer in list_customer" :key="customer.id" :value="customer.id">{{customer.nom}} {{customer.prenom}}</option>
                                    </select>
                                </div>
                                <div class="input-group input-group-lg m-2">
                                    <span class="input-group-text">Date d'arrivée</span>
                                    <input type="date" class="form-control" placeholder="01-01-2000" v-model="date_r">
                                </div>
                                <div class="m-2">
                                    <h4 class="text-muted">Statut de la Réservation</h4>
                                    <div class="form-check mb-1 me-sm-2">
                                        <input type="checkbox" class="form-check-input" v-model="statut" :value="2">
                                        <label class="form-check-label fs-5">Valider</label>
                                    </div>
                                </div>
                                <div class="modal-footer m-1"> 
                                    <button type="button" class="btn btn-primary" @click="setLoadReservation()">Ajouter</button>
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
import Swal from "sweetalert2";
import EventBus from "../eventBus";
export default {
    data(){
        return{
            id_room:0,
            id_customer:0,
            statut:1,
            date_n:null,
            date_r: '',
            list_room:[],
            list_customer:[]
            
        }
    },
    mounted(){
        EventBus.$on('updateReservation',()=>{
            this.loadListRoom();
            this.loadListCustomers();
        })
    },
    beforeMount(){
        this.loadListRoom();
        this.loadListCustomers();
    },
    methods:{
        async setLoadReservation(){
            if(this.statut==true){
                var status=2;
            }else{
                var status=1;
            }
            const dataForm={
                idRoom:this.id_room,
                idCustomer:this.id_customer,
                dateR:this.date_r,
                statut:status
            }
            const dataHeaders={
                method:"POST",
                headers:{"content-Type":"application/json"},
                body:JSON.stringify(dataForm)
            }
            const response = await fetch('/sendReservation/',dataHeaders);
            const data = await response.json();
            EventBus.$emit("reloadReserver");
            this.closeModal();
            Swal.fire({
                icon: data.statut,
                text: data.msg,
                confirmButtonColor:'#3b7ddd'
            });
            this.loadListCustomers();
            this.loadListRoom();
        },
        async loadListRoom(){
            const response = await fetch('/getloadListOccupation/0');
            const data = await response.json();
            this.list_room=data;
        },
        async loadListCustomers(){
            const response = await fetch('/loadListCustomers/0');
            const data = await response.json();
            this.list_customer=data;
            
        },
        closeModal(){
            this.id_customer=0;
            this.id_room=0;
            this.statut=1;
            this.$emit('close');
        }
    },
    components:{
        Swal,EventBus
    }
}
</script>
