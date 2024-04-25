<script>

import Navbar from "./layouts/Navbar.vue";
export default {
    components: {
        Navbar,
    },
};
</script>
<style>


</style>
<template>
   <Navbar></Navbar>

    <div class="container-fluid">

        <div class="row">

            <!-- <Content>
                <Dashboard></Dashboard>
            </Content> -->
            <router-view></router-view>
        </div>
    </div>
</template>

