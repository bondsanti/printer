<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";


const items = ref([]);
const progress = ref(0);
const isLoading = ref(false);
const isLoadingAddData= ref(false);


onMounted(async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/api/data");
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }
});


</script>

<template>
    <div class="mt-3">

        <div class="row">
            <div class="col-12">

                <div class="card border-info">
                    <div class="card-header bg-info bg-gradient">
                        รายชื่อผู้ใช้งานระบบ
                        <div class="btn-group me-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#exampleModal"
                            >
                                <i class="fas fa-users"></i> Add User
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-striped table-hover table-sm"
                            >
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">#</th>
                                        <th scope="col">รหัสพนักงาน</th>
                                        <th scope="col">แผนก</th>
                                        <th scope="col">ชื่อ-นามสกุล</th>
                                        <th scope="col">สิทธิใช้งาน</th>
                                        <th scope="col">แก้ไข</th>

                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="items.length === 0">
                                        <td colspan="6" class="text-center">
                                            ไม่พบข้อมูล
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div
        class="modal fade"
        id="exampleModal"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data</h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select class="form-select" id="printer" name="printer">
                            <option value="">เลือก Printer</option>
                            <option value="Fuji24">Fuji ชั้น 24</option>
                            <option value="Fuji25">Fuji ชั้น 25</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <input
                            type="file"
                            class="form-control"
                            id="fileUpload"
                            name="fileUpload"
                            accept=".xls,.xlsx"
                        />
                    </div>
                    <div
                    class="alert alert-warning d-flex align-items-center"
                    role="alert"
                >
                    <svg
                        class="bi flex-shrink-0 me-2"
                        width="24"
                        height="24"
                        role="img"
                        aria-label="Warning:"
                    >
                        <use xlink:href="#exclamation-triangle-fill" />
                    </svg>
                    <div>Limit Import 2,000 Record !!!</div>
                </div>

                </div>
                <div class="modal-footer">
                    <button
                        v-if="!isLoadingAddData"
                        type="button"
                        class="btn btn-primary"
                        @click="addData"
                    >
                        Save
                    </button>
                    <button
                        v-else
                        class="btn btn-primary"
                        type="button"
                        disabled
                    >
                        <span
                            class="spinner-border spinner-border-sm"
                            role="status"
                            aria-hidden="true"
                        ></span>
                        <span class="visually-hidden">Loading...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
