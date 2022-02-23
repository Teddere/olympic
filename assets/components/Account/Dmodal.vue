<template>
  <div class="modall-backdrop">
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
              <slot name="header" class="modal-title">Gestion de Droits</slot>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="closeModal()"
              ></button>
            </header>

            <section class="modal-body m-2">
              <form class="container-fluid mb-0">
                <div class="row" v-for="ac_menu in list_menu" :key="ac_menu.id">
                  <h4>{{ ac_menu.name }}</h4>
                  <div
                    class="col-12 col-md-6"
                    v-for="ac_sbmenu in ac_menu.sous_menu"
                    :key="ac_sbmenu.id"
                  >
                    <label class="form-check form-check-inline">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :id="ac_menu.id + '_' + ac_sbmenu.id"
                        :value="ac_menu.id + '_' + ac_sbmenu.id"
                        v-model="acMenu"
                        v-if="ac_sbmenu.is_checked == 1"
                        checked
                      />
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :id="ac_menu.id + '_' + ac_sbmenu.id"
                        :value="ac_menu.id + '_' + ac_sbmenu.id"
                        v-model="acMenu"
                        v-else
                      />
                      <span class="form-check-label">
                        {{ ac_sbmenu.name }}
                      </span>
                    </label>
                  </div>
                  <hr />
                </div>

                <div class="modal-footer m-1">
                  <button
                    type="button"
                    class="btn btn-primary"
                    @click="validadtion()"
                  >
                    Valider
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
</style>
<script>
import EventBus from "../eventBus";
import Swal from "sweetalert2";
export default {
  data() {
    return {
      account_id: 0,
      list_menu: [],
      acMenu: [],
    };
  },
  mounted() {
    EventBus.$on("accontMenu_id", (id_account) => {
      this.account_id = id_account;
      this.loadMenu();
    this.loadAccountMenu(id_account);
    });
  },
  methods: {
    closeModal() {
      this.$emit("close");
    },
    async loadMenu() {
      const response = await fetch("/getloadMenu/" + this.account_id);
      const data = await response.json();
      console.log(data);
      this.list_menu = data;
    },
    async loadAccountMenu(id) {
      const response = await fetch("/loadAccountMenu/" + id);
      const data = await response.json();
      this.acMenu = data;
    },
    async validadtion(){
      const dataFrom={
          validate:this.acMenu,
          id_account:this.account_id
      }
      const dataHeaders={
        method:"POST",
        headers:{"content-Type":"application/json"},
        body: JSON.stringify(dataFrom)
      }
      const response= await fetch('/validadtionDroit/',dataHeaders);
      const data = await response.json();
      Swal.fire(data.msg);
      this.closeModal()
    }
  },
  components: {
    EventBus,Swal
  },
};
</script>