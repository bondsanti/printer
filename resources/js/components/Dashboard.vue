<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
import Chart from "../components/Chart.vue";
import ChartPie from "../components/ChartPie.vue";

const items = ref([]);
const progress = ref(0);
const isLoading = ref(false);
const isImportLoading = ref(false);

onMounted(async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/api/data");
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.error(error);
    }
});

const uploadData = async () => {
    const printer = document.getElementById("printer").value;
    const fileInput = document.getElementById("fileUpload");
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append("excel_file", file);
    formData.append("printer", printer);

    try {
        isImportLoading.value = true;
        const response = await axios.post("/api/import-excel", formData, {
            // onUploadProgress: (progressEvent) => {
            //     if (progressEvent.lengthComputable) {
            //         progress.value = Math.round(
            //             (progressEvent.loaded * 100) / progressEvent.total
            //         );
            //     }
            // },
        });
        isImportLoading.value = false;
        Swal.fire({
            icon: "success",
            title: "Success",
            text: response.data.message,
            showConfirmButton: false,
            timer: 1000,
        }).then(() => {
            setTimeout(() => {
                location.reload(); // รีโหลดหน้าเว็บ
            }, 200);
        });
    } catch (error) {
        //console.error(error);
        isImportLoading.value = false;
        Swal.fire({
            icon: "error",
            title: "Error",
            text: error.response.data.message,
        });
    } finally {
        progress.value = null;
    }
};
</script>

<template>
    <div class="mt-3">
        <div class="row mb-3">
            <Chart></Chart>
        </div>
        <div class="row mb-3">
            <ChartPie></ChartPie>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info bg-gradient">
                        ข้อมูล Log Printer ทั้งหมด
                        <div class="btn-group me-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#exampleModal"
                            >
                                <i class="fas fa-file-excel"></i> Import
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">#</th>
                                        <th scope="col">วันที่</th>
                                        <th scope="col">เวลา</th>
                                        <th scope="col">เครื่องพิมพ์</th>
                                        <th scope="col">ประเภทงาน</th>
                                        <th scope="col">รหัสพนักงาน</th>
                                        <th scope="col">ชื่อ-นามสกุล</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">ขาวดำ</th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="items.length === 0">
                                        <td colspan="9" class="text-center">
                                            ไม่พบข้อมูล
                                        </td>
                                    </tr>
                                    <tr
                                        v-else
                                        v-for="(item, index) in items"
                                        :key="index"
                                        class="text-center"
                                    >
                                        <td class="text-center">
                                            {{ item.id }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.date }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.time }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.printername }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.jobtype }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.code_user }}
                                        </td>
                                        <td class="text-left">
                                            {{ item.user_ref ? item.user_ref.name_eng  : '-' }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_color }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_bw }}
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

                    <!-- <div
                        class="progress"
                        v-if="progress !== null && progress !== 0"
                    >
                        <div
                            class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                            role="progressbar"
                            :style="{ width: progress + '%' }"
                            :aria-valuenow="progress"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >
                            {{ progress }}%
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button
                        v-if="!isImportLoading"
                        type="button"
                        class="btn btn-primary"
                        @click="uploadData"
                    >
                        Upload Data
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


