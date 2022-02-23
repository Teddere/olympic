<template>
    <div class="modall-backdrop">
        <div class="card" role="dialog" aria-labelledby="modallTitle" aria-describedby="modallDescription">
            <div class="modall">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content">
                        <header class="modal-header" id="modallTitle">
                            <slot name="header" class="modal-title" v-if="status==1">Réservation Annulée</slot>
                            <slot name="header" class="modal-title" v-else>Réservation Validée</slot>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"></button>
                        </header>
                        <section class="modal-body m-2" v-if="displayedPosts.length > 0">
                            <div class="table-responsive">
                                <table class="table table-xs mb-0">
                                    <thead>
                                        <tr>
                                            <th>Réservation</th>
                                            <th>Client</th>
                                            <th>Chambre</th>
                                            <th>Date de départ</th>
                                            <th>Date d'arrive</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="valide in displayedPosts" :key="valide.id">
                                            <td>Réservation</td>
                                            <td>Réservation</td>
                                            <td>Réservation</td>
                                            <td>Réservation</td>
                                            <td>Réservation</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return{
            posts: [],
            page: 1,
            perPage: 5,
            pages: [],
        }
    },
    methods:{
        async listValidation(){
            const response = await fetch('')
        },
        paginate(posts){
            let page = this.page;
            let perPage = this.perPage;
            let from = page * perPage - perPage;
            let to = page * perPage;
            return posts.slice(from, to);
        },
        setPages(){
        let filter=Array();
        let numberOfPages = Math.ceil(this.posts.length / this.perPage);
        for (let index = 1; index <= numberOfPages; index++) {
            filter.push(index);
        }
        this.pages=Array.from(new Set(filter));
        },
    },
    computed:{
        displayedPosts() {
            return this.paginate(this.posts);;
        }
    },
    watch: {
        posts() {
            this.setPages();
        }
    },
}
</script>