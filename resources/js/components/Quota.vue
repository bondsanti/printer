<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";
const items = ref([]);
const roleUser = ref([]);
const progress = ref(0);
const isLoading = ref(false);
const isImportLoading = ref(false);

const fetchRole = async () => {
    try {
        const response = await axios.get("/role/users");
        roleUser.value = response.data.data;
        //console.log(roleUser.value);
    } catch (error) {
        console.log("Error fetching loginId:", error);
    }
};

const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/api/data/quota");
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value );
    } catch (error) {
        console.log(error);
    }
};

onMounted(async () => {
    fetchRole();
    fetchData();
});

const uploadData = async () => {
    const fileInput = document.getElementById("fileUpload");
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append("excel_file", file);

    try {
        isImportLoading.value = true;
        const response = await axios.post("/api/quota/import-excel", formData, {
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
        <div class="row">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info bg-gradient">
                        ข้อมูลโควตา
                        <div
                            class="btn-group me-2"
                            v-if="roleUser && roleUser.role_type !== 'User'"
                        >
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
                            <table
                                class="table table-striped table-hover table-sm"
                            >
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col" rowspan="2">#</th>
                                        <th scope="col" rowspan="2">
                                            รหัสพนักงาน
                                        </th>
                                        <th scope="col" rowspan="2">แผนก</th>
                                        <th scope="col" rowspan="2">
                                            ชื่อ-นามสกุล
                                        </th>
                                        <th scope="col" colspan="2" >
                                            เครื่องพิมพ์ 24
                                        </th>
                                        <th scope="col" colspan="2">
                                            เครื่องพิมพ์ 25
                                        </th>
                                    </tr>
                                    <tr class="text-center">
                                        <th scope="col" >สี</th>
                                        <th scope="col" >ขาวดำ</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">ขาวดำ</th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="items.length === 0">
                                        <td colspan="8" class="text-center">
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
                                            {{ index + 1 }}
                                        </td>

                                        <td class="text-center">
                                            {{ item.code }}
                                        </td>
                                        <td class="text-left">
                                            {{
                                                item.user_ref
                                                    ? item.user_ref.dep_ref.name
                                                    : item.department
                                            }}
                                        </td>
                                        <td class="text-left">
                                            {{
                                                item.user_ref
                                                    ? item.user_ref.name_eng
                                                    : item.name
                                            }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_color_24 }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_bw_24 }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_color_25 }}
                                        </td>
                                        <td class="text-center">
                                            {{ item.total_bw_25 }}
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
                    </div> -->
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
