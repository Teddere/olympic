<template>
    <div class="modall-backdrop">
        <transition name="fade">
            <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
                <div class="modall">
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <header class="modal-header" id="modallTitle">
                                <slot name="header" class="modal-title">Modification de la date d'arrivée</slot>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"></button>
                            </header>
                            <section class="modal-body m-2">
                                <form class="mb-0">
                                    <div class="input-group input-group-lg m-2">
                                        <span class="input-group-text">Date d'arrivée</span>
                                        <input type="date" class="form-control" placeholder="01-01-2000" v-model="date">
                                    </div>
                                    <div class="modal-footer m-1">
                                        <button type="button" class="btn btn-primary" @click="addIdReseve()">Modifier</button>
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
import EventBus from"../eventBus";
export default {
    data(){
        return{
            id_reserve:0,
            date:''
        }
    },
    mounted(){
        EventBus.$on('idLoadReserve',(id)=>{
            this.id_reserve=id;
            this.modifReserve();
        });
    },
    methods:{
        async modifReserve(){
            const response = await fetch('/loadIdReserve/'+this.id_reserve);
            const data = await response.json();
            this.date=data.date;
        },
        async addIdReseve(){
            const dataForm={
                id:this.id_reserve,
                date_r:this.date
            }
            const dataHeaders={
                method:"POST",
                Headers:{'content-Type':'application/json'},
                body:JSON.stringify(dataForm)
            }
            const response = await fetch('/setIdReserve/',dataHeaders);
            const data = await response.json();
            EventBus.$emit('reloadReserver');
            this.closeModal();
            Swal.fire({
                icon: data.statut,
                text: data.msg,
                confirmButtonColor:'#3b7ddd'
            });
            
        },
        closeModal(){
            this.id_reserve=0;
            this.date='';
            this.$emit('close');
        }
    },
    components:{
      EventBus,Swal 
    }
}
</script>