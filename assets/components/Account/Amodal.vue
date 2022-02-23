<template>
  <div class="modall-backdrop">
    <transition name="fade">
      <div
        class="card"
        role="dialog"
        aria-labelledby="modallTitle"
        aria-describedby="modallDescription"
      >
        <!--Ajout personnel-->
        <div class="modall">
          <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
              <header class="modal-header" id="modallTitle">
                <slot name="header" class="modal-title" v-if="id_account == 0">
                  Nouveau compte
                </slot>
                <slot name="header" class="modal-title" v-else
                  >Modification des informations</slot
                >
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  @click="closeModal()"
                ></button>
              </header>

              <section class="modal-body m-2">
                <form class="mb-0" >
                  <div class="input-group m-2">
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Veuillez entrer le nom"
                      v-model="lname"
                    />
                  </div>
                  <div class="input-group m-2">
                    <input
                      type="text"
                      class="form-control"
                      placeholder="Veuillez entrer le Prénom"
                      v-model="fname"
                    />
                  </div>

                  <div class="input-group m-2">
                    <input
                      type="email"
                      class="form-control"
                      placeholder="Veuillez entrer l'adresse email"
                      v-model="email"
                    />
                  </div>
                  <div class="input-group m-2">
                    <input
                      type="text"
                      class="form-control"
                      id="login"
                      placeholder="Veuillez entrer le poste"
                      v-model="job"
                    />
                  </div>
                  <div class="input-group m-2">
                    <input  id="file" type="file"  class="form-control"  @change="loadAvatar" reset/>
                  </div>


                  <div class="input-group m-2">
                    <label class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        value="1"
                        v-model="status"
                        v-if="status > 0"
                        checked
                      />
                      <input
                        class="form-check-input"
                        type="radio"
                        value="1"
                        v-model="status"
                        v-else
                      />
                      <span class="form-check-label"> Active </span>
                    </label>
                    <label class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="radio"
                        value="0"
                        v-model="status"
                        v-if="status == 0"
                        checked
                      />
                      <input
                        class="form-check-input"
                        type="radio"
                        value="0"
                        v-model="status"
                        v-else
                      />
                      <span class="form-check-label"> Desactive </span>
                    </label>
                  </div>
                  <div class="modal-footer m-1">
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click="addAndModif()"
                      v-if="id_account == 0"
                    >
                      Ajouter
                    </button>
                    <button
                      type="button"
                      class="btn btn-primary"
                      @click="addAndModif()"
                      v-else
                    >
                      Modifier
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      @click="closeModal()"
                    >
                      Annuler
                    </button>
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
<style>
.modall-backdrop {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modall {
  height: 100%;
  left: 0;
  outline: 0;
  overflow-x: hidden;
  overflow-y: auto;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1055;
}
.fade-enter-active, .fade-leave-active{
  transition: all .3s ease;
}
.slide-fade-enter, .slide-fade-leave-to{
  transform: translateX(10px);
}
</style>
<script>
import EventBus from "../eventBus";
import Swal from "sweetalert2";
export default {
  name: "Amodal",
  data() {
    return {
      id_account: 0,
      lname: "",
      fname: "",
      email: "",
      status: 0,
      job: "",
      image:{avatar:''}
    };
  },
  mounted() {
    EventBus.$on("load_id", (id) => {
      this.loadAcount(id);
    });
  },
  methods: {
    loadAvatar(event){
      
     let file=new FileReader();

    file.readAsDataURL(event.target.files[0]);

    file.onload = (event) =>{
      this.image.avatar=event.target.result
    }

    },
    closeModal() {
      let file =document.getElementById('file');
      file.reset;
      this.id_account = 0;
      this.lname = "";
      this.fname = "";
      this.email = "";
      this.status = 0;
      this.job = "";
      this.$emit("close");
    },
    async loadAcount(id_account) {
      if(id_account > 0){
      const response = await fetch("/loadAcount/" + id_account);
      const data = await response.json();
      this.id_account = data.id;
      this.lname = data.lname;
      this.fname = data.fname;
      this.email = data.email;
      this.status = data.status;
      this.job = data.job;
      }else{
        this.id_account=0;
      }
    },
    async addAndModif() {
      const dataFrom = {
        data_id: this.id_account,
        data_lname: this.lname,
        data_fname: this.fname,
        data_email: this.email,
        data_status: this.status,
        data_job: this.job,
        data_image: this.image,
      };
      const heraderFrom = {
        method: "POST",
        headers: {"content-Type":"application/json" },
        body: JSON.stringify(dataFrom),
      };
      const response = await fetch("/addAndModif/", heraderFrom);
      const data = await response.json();
      this.closeModal();
      EventBus.$emit("reload");
      Swal.fire({
        icon: "success",
        text: data.msg,
        confirmButtonColor:'#3b7ddd'
      });
    },

  },

  components: {
    EventBus,
    Swal,
  },
};
</script>