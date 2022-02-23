<template>
    <div class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Settings</h1>
            <div class="row">
                    <!-- Barre de navigation -->
                <div class="col-md-3 col-xl-2">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Profile Settings</h5>
                        </div>
                        <div class="list-group list-group-flush" role="tablist">
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#account" role="tab" aria-selected="false">
                                Profil
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#account" role="tab" aria-selected="false">
                                Informations Personnelles
                            </a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#password" role="tab" aria-selected="false">
                                Mot de passe
							</a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#" role="tab" aria-selected="false">
								Messages
							</a>
                            <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#" role="tab">
								Notifications
							</a>
                        </div>
                    </div>

                </div>
                <!-- Fin de la barre de navigation -->
                <div class="col-md-9 col-xl-10">
                    <div class="tab-content">
                        <!-- Premier panel -->
                        <!-- <div class="tab-pane fade active show" id="account" role="tabpanel"> -->

                            <!-- Premier composant -->
                            <Person></Person>
                            <!-- Deuxieme comapsant -->
                        <!-- </div> -->
                    <!--Fin Premier panel -->
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Person from "./Person";
export default {
  components:{
      Person
  }  
}
</script>