<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
const roleUser = ref([]);


const fetchRole = async () => {
    try {
        const response = await axios.get("/role/users");
        roleUser.value = response.data.data;
        //console.log(roleUser.value);
    } catch (error) {
        console.log("Error fetching loginId:", error);
    }
};

onMounted(() => {
    fetchRole();
});

const imagePath = "/img/vbe_old.png";
</script>
<template>
    <div>
        <nav
            class="navbar navbar-expand-lg navbar-light"
            style="background-color: #e3f2fd"
        >
            <div class="container-fluid">
                <router-link to="/" class="navbar-brand">
                    <img
                        :src="imagePath"
                        alt=""
                        width="30"
                        height="24"
                        class="d-inline-block align-text-top"
                    />
                    Printer Report
                </router-link>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div
                    class="collapse navbar-collapse"
                    id="navbarSupportedContent"
                >
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <router-link
                                to="/"
                                class="nav-link"
                                aria-current="page"
                                exact
                                active-class="active"
                                ><i class="fas fa-tachometer-alt"></i>
                                หน้าหลัก</router-link
                            >
                        </li>

                        <li class="nav-item">
                            <router-link
                                to="/quota"
                                class="nav-link"
                                aria-current="page"
                                active-class="active"
                                ><i class="fas fa-server"></i>
                                โควตา</router-link
                            >
                        </li>

                        <li class="nav-item">
                            <router-link
                                to="/report"
                                class="nav-link"
                                aria-current="page"
                                active-class="active"
                                ><i class="fas fa-chart-bar"></i>
                                รายงาน</router-link
                            >
                        </li>



                    </ul>
                    <ul class="navbar-nav mb-2 mb-lg-0 justify-content-end">
                        <li class="nav-item">
                            <a
                                href="/logout/auth"
                                class="nav-link"
                                aria-current="page"
                                active-class="active"
                            >
                                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>
