/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import Vue from "vue";
import Navbar from "./components/Navbar";
import Utable from "./components/Account/Utable";
import Rtable from "./components/Reserve/Rtable";
import Ctable from "./components/Residence/Ctable";
import Cutable from "./components/Customer/Cutable";
import Profile from "./components/Profile/Profile";
import Vtable from "./components/Validation/Vtable";



// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const myVue = new Vue({
    el: '#app',
    components: {
        Navbar,
        Utable,
        Rtable,
        Ctable,
        Cutable,
        Vtable,
        Profile
    }
});