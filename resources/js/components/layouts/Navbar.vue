<template>
    <div>
        <nav
            class="navbar navbar-expand-lg navbar-light"
            style="background-color: #e3f2fd"
        >
            <div class="container-fluid">
                <router-link to="/" class="navbar-brand">
                    <img
                    :src="getImagePath()"
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
                    <!-- <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <router-link to="/"
                                class="nav-link"
                                aria-current="page"
                                ><i class="fas fa-list"></i> ตารางข้อมูล</router-link
                            >
                        </li>

                    </ul> -->
                </div>
            </div>
        </nav>
    </div>
</template>
<script>
export default {
    methods: {
        getImagePath() {
            return '/img/vbe_old.png';
        }
    }
}
</script>

