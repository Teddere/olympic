<template>
  <div class="row m-3">
    <div class="col-12 col-md-12 col-xxl-6 d-flex order-3 order-xxl-2">
      <div class="card flex-fill w-100">
        <div class="card-body">
          <div class="card-header">
            <button class="btn btn-primary btn-sm" title="Ajouter un collaborateur" @click="displayVisible(0)">
              <i class="far fa-plus-square"></i>
            </button>
            <h5 class="card-title mb-0">Liste de tous les collaborateurs</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-xs mb-0">
              <thead>
                <tr>
                  <th>Avatar</th>
                  <th>Nom</th>
                  <th>Prénom</th>
                  <th>E-mail</th>
                  <th>Statut</th>
                  <th>Poste</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="account in displayedPosts" :key="account.id">
                  <td>
                    <img
                      :src="'/upload/' + account.avatar"
                      class="avatar img-fluid rounded me-1"
                    />
                  </td>
                  <td>{{ account.lname }}</td>
                  <td>{{ account.fname }}</td>
                  <td>{{ account.email }}</td>
                  <td v-if="account.status==1">
                      <span class="badge bg-success">Actif</span>
                    </td>
                    <td v-else>
                      <span class="badge bg-danger">Inactif</span>
                    </td>
                  <td>{{ account.job }}</td>
                  <td>
                    <a  title="Edit" class="align-middle text-dark" @click="displayVisible(account.id)">
                      <i class="far fa-edit"></i></a
                    >&nbsp;
                    <a href="#" title="Delete" class="align-middle text-dark" @click="deleteAccount(account.id)"
                      ><i class="fas fa-trash-alt"></i></a
                    >&nbsp;
                    <a href="#" title="Droit" class="align-middle text-dark" @click="displayDVisible(account.id)"
                      ><i class="far fa-check-square"></i
                    ></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 ms-auto">
            <nav aria-label="PdataTables_paginate paging_simple_numbers">
              <ul class="pagination">
                <li class="page-item">
                  <a
                    type="button"
                    class="page-link"
                    v-if="page != 1"
                    @click="page--"
                  >
                    &laquo;
                  </a>
                </li>
                <li
                  class="page-item el"
                  v-for="pageNumber in pages.slice(page - 1, page + 5)"
                  :key="pageNumber.id"
                  @click="page = pageNumber"
                >
                  <a
                    class="page-link text-dark active"
                    
                  >
                    {{ pageNumber }}
                  </a>

                </li>
                <li class="page-item">
                  <a
                    @click="page++"
                    v-if="page < pages.length"
                    class="page-link"
                  >
                   &raquo;
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
      <Amodal v-show="isModalVisible" @close="closeModal()"></Amodal>
      <Dmodal v-show="isModalVisible2" @close="closeDmodal()"></Dmodal>
  </div>
</template>
<script>
import EventBus from '../eventBus';
import Amodal from './Amodal';
import Swal from "sweetalert2";
import Dmodal from "./Dmodal";
export default {
  data() {
    return {
      posts: [],
      page: 1,
      perPage: 4,
      pages: [],
      isModalVisible:false,
      isModalVisible2:false,
    };
  },
  beforeMount() {
    this.loadAccount();
        EventBus.$on('reload',()=>{
        this.loadAccount();
      });
  },
  methods: {
    async loadAccount() {
      const response = await fetch("/listAccount/");
      const data = await response.json();
      this.posts = data;      
    },
    setPages() {
      let filter=Array();
      let numberOfPages = Math.ceil(this.posts.length / this.perPage);
      for (let index = 1; index <= numberOfPages; index++) {
        filter.push(index);
      }
        this.pages=Array.from(new Set(filter));
      
    },
    paginate(posts) {
      
        let page = this.page;
        let perPage = this.perPage;
        let from = page * perPage - perPage;
        let to = page * perPage;
      return posts.slice(from, to);
      

    },
    displayVisible(id) {
      this.isModalVisible = true;
      EventBus.$emit('load_id',id);
    },
    closeModal() {
      this.isModalVisible = false;
    },
    displayDVisible(id){
      this.isModalVisible2 = true;
      EventBus.$emit('accontMenu_id',id);
    },
    closeDmodal(){
      this.isModalVisible2 = false;
    },
    deleteAccount(account_id){
      Swal.fire({
        title: "Suppression",
        text: "Êtes-vous sûre de vouloir supprimer ce compte ?",
        icon: "error",
        showCancelButton: true,
        confirmButtonColor: "#3b7ddd",
        cancelButtonColor: "#495057",
        confirmButtonText: "OUI",
        cancelButtonText: "NON",
      }).then((result) => {
        if (result.isConfirmed) {
          this._deleteAccount(account_id);
        }else{
          this.closeModal();
        }
      });
    },
    async _deleteAccount(id){
      const response = await fetch('/deleteAccount/'+id);
      const data = await response.json();
      this.loadAccount();
      Swal.fire({
        title:data.msg ,
        icon: data.statut,
        confirmButtonColor: "#3b7ddd",
      })

    }
  },
  computed: {
    //   Afficher le tableau de contenu
    displayedPosts() {

      return this.paginate(this.posts);
    },
  },
  components:{
    Amodal,EventBus,Dmodal
  },
  watch: {
    //   Afficher la navigation
    posts() {
      this.setPages();
    },
  },

};
</script>
<style>
.container {
  padding: 2em;
}
</style>

