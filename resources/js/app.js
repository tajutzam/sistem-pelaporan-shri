import "./bootstrap";

import.meta.glob(["../images/**"]);

import Alpine from "alpinejs";
window.Alpine = Alpine;
Alpine.start();

import "@fortawesome/fontawesome-free/css/all.min.css";

import Swal from "sweetalert2";

window.Swal = Swal;
