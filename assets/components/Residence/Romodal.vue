<template>
    <div class="modall-backdrop">
        <transition name="fade">
        <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
            <div class="modall">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <header class="modal-header" id="modallTitle">
                            <slot name="header" class="modal-title" v-if="id==0">Nouvelle chambre</slot>
                            <slot name="header" class="modal-title" v-else>Modification de chambre</slot>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"></button>
                        </header>
                        <section class="modal-body m-2">
                            <form class="mb-0">
                                <div class="input-group m-2">
                                    <input type="text" class="form-control" placeholder="Veuillez entrer le nom de chambre" v-model="name"/>
                                </div>
                                <div class="input-group m-2">
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="true" v-model="statut" v-if="statut==true" checked>
                                        <input class="form-check-input" type="radio" value="true" v-model="statut" v-else>
                                        <span class="form-check-label">Occuper</span>
                                    </label>
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" value="false" v-model="statut" v-if="statut==false" checked>
                                        <input class="form-check-input" type="radio" value="false" v-model="statut" v-else>
                                        <span class="form-check-label">Libre</span>
                                    </label>

                                </div>
                                <div class="modal-footer m-1">
                                    <button type="button" class="btn btn-primary" @click="addAndMotifRoom()" v-if="id==0">Ajouter</button>
                                    <button type="button" class="btn btn-primary" @click="addAndMotifRoom()" v-else>Modifier</button>
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
            id:0,
            name:'',
            statut:false
        }
    },
    mounted(){
        EventBus.$on('id_loadRoom',(id)=>{
            this.id=id;
            this.loadElementRoom();
        });
    },
    methods:{
        async loadElementRoom(){
            const response=await fetch('/getloadElementRoom/'+this.id);
            const datas = await response.json();
            if(datas.length > 0){
                const data = datas[0];
                this.id=data.id;
                this.name=data.name;
                this.statut=data.status;
            }
        },
        async addAndMotifRoom(){
            const dataForm={
                id:this.id,
                nom:this.name,
                statut:this.statut
            }
            const dataHeaders={
                method:'POST',
                headers:{"Content-Type":"application/json"},
                body:JSON.stringify(dataForm)
            }
            const response=await fetch('/setElementRoom/',dataHeaders);
            const data = await response.json();
            this.closeModal();
            EventBus.$emit('loadRoom');
            Swal.fire({
                        icon:'success',
                        text:data,
                        confirmButtonColor:'#3b7ddd'
                     });
        },
        closeModal(){
            this.id=0;
            this.name='';
            this.statut=0;
            this.$emit('close');
        }
    },
    components:{
        EventBus,Swal
    }
}
</script>