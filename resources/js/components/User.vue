<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const items = ref([]);
const isLoading = ref(false);
const isLoadingAddData = ref(false);
const roleUser = ref([]);
const editUser = reactive({
    id: "",
    code: "",
    role_type: "",
    active: "",

});

const fetchRole = async () => {
    try {
        const response = await axios.get("/role/users");
        roleUser.value = response.data.data;
        // console.log(roleUser.value);
    } catch (error) {
        console.log("Error fetching loginId:", error);
    }
};

const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/data/users");
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

const addData = async () => {
    const role_type = document.getElementById("role_type").value;
    const code = document.getElementById("code").value;

    const formData = new FormData();
    formData.append("role_type", role_type);
    formData.append("code", code);

    try {
        isLoadingAddData.value = true;
        const response = await axios.post("/role/add-users", formData);
        isLoadingAddData.value = false;
        Swal.fire({
            icon: "success",
            title: "Success",
            text: response.data.message,
            showConfirmButton: false,
            timer: 1000,
        }).then(() => {
            setTimeout(() => {
                location.reload(); // รีโหลดหน้าเว็บ
            }, 100);
        });
    } catch (error) {
        const errorMessage = error.response.data;
        let errorText = "";
        if (errorMessage && errorMessage.errors) {
            Object.values(errorMessage.errors).forEach((errors) => {
                errors.forEach((error) => {
                    errorText += `${error}\n`;
                });
            });
        } else if (errorMessage && errorMessage.message) {
            errorText = errorMessage.message;
        } else {
            errorText = "เกิดข้อผิดพลาดในการส่งข้อมูล";
        }

        Swal.fire({
            icon: "error",
            title: "Error",
            text: errorText,
        });
        isLoadingAddData.value = false;
    }
};

const deleteItem = async (item) => {
    try {
        const result = await Swal.fire({
            title: "คุณต้องการลบรายการนี้หรือไม่?",
            text: "การดำเนินการนี้ไม่สามารถยกเลิกได้!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "ใช่, ลบ!",
            cancelButtonText: "ยกเลิก",
        });

        if (result.isConfirmed) {
            const response = await axios.post(`/role/del-users/${item.id}`);
            // const index = roleUser.value.indexOf(item);
            // roleUser.value.splice(index, 1);
            // console.log("Item deleted:", response.data);
            Swal.fire({
                icon: "success",
                title: "Success",
                text: response.data.message,
                showConfirmButton: false,
                timer: 1000,
            }).then(() => {
                setTimeout(() => {
                    location.reload(); // รีโหลดหน้าเว็บ
                }, 100);
            });
        }
    } catch (error) {
        console.log("Error deleting item:", error);
        Swal.fire("เกิดข้อผิดพลาด!", "ไม่สามารถลบรายการได้", "error");
    }
};

const editItem = async (item) => {
    editUser.id = item.id;
    editUser.code = item.user_ref.code;
    editUser.role_type = item.role_type;
    editUser.active = item.active;

    //console.log(editUser.code);
    const modal = document.querySelector("#EditModal");

    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
};

const updateData = async () => {
    const role_type = document.getElementById("role_typee").value;
    const code = document.getElementById("codee").value;
    const id = document.getElementById("id").value;
    const active = document.getElementById("active").value;

    //console.log(active);

    const formData = new FormData();
    formData.append("role_typee", role_type);
    formData.append("codee", code);
    formData.append("active", active);


    try {
        const response = await axios.post(`/role/update-users/${id}`, formData);
        Swal.fire({
            icon: "success",
            title: "Success",
            text: response.data.message,
            showConfirmButton: false,
            timer: 1000,
        }).then(() => {
            setTimeout(() => {
                location.reload(); // รีโหลดหน้าเว็บ
            }, 100);
        });
    } catch (error) {
        const errorMessage = error.response.data;
        let errorText = "";
        if (errorMessage && errorMessage.errors) {
            Object.values(errorMessage.errors).forEach((errors) => {
                errors.forEach((error) => {
                    errorText += `${error}\n`;
                });
            });
        } else if (errorMessage && errorMessage.message) {
            errorText = errorMessage.message;
        } else {
            errorText = "เกิดข้อผิดพลาดในการส่งข้อมูล";
        }

        Swal.fire({
            icon: "error",
            title: "Error",
            text: errorText,
        });
        isLoadingAddData.value = false;
    }
};
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
                                        <th scope="col">ตำแหน่ง</th>
                                        <th scope="col">ชื่อ-นามสกุล</th>
                                        <th scope="col">สิทธิใช้งาน</th>
                                        <th scope="col">สถานะ</th>
                                        <th scope="col">แก้ไข</th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr v-if="items.length === 0">
                                        <td colspan="7" class="text-center">
                                            ไม่พบข้อมูล
                                        </td>
                                    </tr>
                                    <tr
                                        v-else
                                        v-for="(item, index) in items"
                                        :key="index"
                                        class="text-center"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.user_ref.code }}</td>
                                        <td>
                                            {{
                                                item.user_ref.position_ref.name
                                            }}
                                        </td>
                                        <td>{{ item.user_ref.name_eng }}</td>
                                        <td>{{ item.role_type }}</td>
                                        <td>
                                            {{
                                                item.active == "1"
                                                    ? "active"
                                                    : "Disable"
                                            }}
                                        </td>
                                        <td>
                                            <button
                                                v-if="
                                                    item.user_id !==
                                                    roleUser.user_id
                                                "
                                                @click="editItem(item)"
                                                class="btn btn-primary btn-sm"
                                            >
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button
                                                v-if="
                                                    item.user_id !==
                                                    roleUser.user_id
                                                "
                                                @click="deleteItem(item)"
                                                class="btn btn-danger btn-sm"
                                            >
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
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
                    <h5 class="modal-title">Add User</h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select
                            class="form-select"
                            id="role_type"
                            name="role_type"
                        >
                            <option value="">เลือก สิทธิใช้งาน</option>
                            <option value="SuperAdmin">SuperAdmin</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="code"
                            name="code"
                            placeholder="รหัสพนักงาน"
                        />
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

    <!-- Modal Edit -->
    <div
        class="modal fade"
        id="EditModal"
        tabindex="-1"
        aria-labelledby="editLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select
                            class="form-select"
                            id="role_typee"
                            name="role_typee"
                            v-model="editUser.role_type"
                        >
                            <option value="">เลือก สิทธิใช้งาน</option>
                            <option value="SuperAdmin">SuperAdmin</option>
                            <option value="Admin">Admin</option>
                            <option value="User">User</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <input
                            type="hidden"
                            class="form-control"
                            id="codee"
                            name="codee"
                            placeholder="รหัสพนักงาน"
                            v-model="editUser.code"
                        />
                        <select
                            class="form-select"
                            id="active"
                            name="active"
                            v-model="editUser.active"
                        >
                            <option value="1">Active</option>
                            <option value="0">Disable</option>

                        </select>

                        <input
                            type="hidden"
                            class="form-control"
                            id="id"
                            name="id"
                            v-model="editUser.id"
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        v-if="!isLoadingAddData"
                        type="button"
                        class="btn btn-primary"
                        @click="updateData"
                    >
                        Update
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
