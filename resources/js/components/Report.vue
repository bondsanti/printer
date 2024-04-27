<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const items = ref([]);
const progress = ref(0);
const isLoading = ref(false);
const startDate = ref("");
const endDate = ref("");

onMounted(async () => {
    const currentDate = new Date();
    const firstDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1
    );
    const lastDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth() + 1,
        0
    );

    startDate.value = formatDate(firstDayOfMonth);
    endDate.value = formatDate(currentDate);

    console.log(startDate.value);

    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
            },
        });
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }
});
function formatDate(date) {
    const year = date.getFullYear();
    let month = date.getMonth() + 1;
    if (month < 10) {
        month = "0" + month;
    }
    let day = date.getDate();
    if (day < 10) {
        day = "0" + day;
    }
    return `${year}-${month}-${day}`;
}

function updateStartDate(value) {
    startDate.value = value;
}

function updateEndDate(value) {
    endDate.value = value;
}
</script>

<template>
    <div class="mt-3">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label"
                                    >วันเริ่มต้น</label
                                >
                                <input
                                    type="date"
                                    class="form-control"
                                    id="startDate"
                                    :value="startDate"
                                    @input="
                                        updateStartDate($event.target.value)
                                    "
                                />
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label"
                                    >วันสิ้นสุุด</label
                                >
                                <input
                                    type="date"
                                    class="form-control"
                                    id="endDate"
                                    :value="endDate"
                                    @input="updateEndDate($event.target.value)"
                                />
                            </div>

                            <div
                                class="cd-grid gap-2 d-md-flex justify-content-md-center"
                            >
                                <button
                                    type="submit"
                                    class="btn btn-primary mr-2"
                                >
                                    ตกลง
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    ยกเลิก
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info bg-gradient">
                        ตารางสรุปรายการใช้งานเครื่องพิมพ์
                    </div>

                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">User Code</th>
                                    <th scope="col">User Name</th>
                                    <th scope="col">Total Color</th>
                                    <th scope="col">Total BW</th>
                                    <th scope="col">Total</th>
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
                                        No data available
                                    </td>
                                </tr>
                                <tr
                                    v-else
                                    v-for="(department, index) in items"
                                    :key="index"
                                    class="text-center"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ department.department_name }}</td>
                                    <td>
                                        <template
                                            v-for="(
                                                user, key
                                            ) in department.users"
                                            :key="key"
                                        >
                                            <div>{{ user.code }}</div>
                                        </template>
                                    </td>
                                    <td>
                                        <template
                                            v-for="(
                                                user, key
                                            ) in department.users"
                                            :key="key"
                                        >
                                            <div>{{ user.name }}</div>
                                        </template>
                                    </td>
                                    <td>
                                        <template
                                            v-for="(
                                                user, key
                                            ) in department.users"
                                            :key="key"
                                        >
                                            <div>{{ user.total_color }}</div>
                                        </template>
                                    </td>
                                    <td>
                                        <template
                                            v-for="(
                                                user, key
                                            ) in department.users"
                                            :key="key"
                                        >
                                            <div>{{ user.total_bw }}</div>
                                        </template>
                                    </td>
                                    <td>
                                        <template
                                            v-for="(
                                                user, key
                                            ) in department.users"
                                            :key="key"
                                        >
                                            <div>{{ user.total }}</div>
                                        </template>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td>sumtotal_color</td>
                                    <td>sumtotal_bw</td>
                                    <td>sumtotal</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- <table class="table table-striped table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">#</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">code</th>
                                    <th scope="col">Users</th>
                                    <th scope="col">Total Color</th>
                                    <th scope="col">Total BW</th>
                                    <th scope="col">Total</th>
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
                                <tr
                                    v-else
                                    v-for="(department, index) in items"
                                    :key="index"
                                    class="text-center"
                                >
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ department.department_name }}</td>
                                    <td></td>
                                    <td >
                                        <table class="table text-center">
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        user, key
                                                    ) in department.users"
                                                    :key="key"
                                                >
                                                    <td>{{ user.code }}</td>
                                                    <td>{{ user.name }}</td>
                                                    <td>
                                                        {{ user.total_color }}
                                                    </td>
                                                    <td>{{ user.total_bw }}</td>
                                                    <td>{{ user.total }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td>{{ department.total_color }}</td>
                                    <td>{{ department.total_bw }}</td>
                                    <td>{{ department.total }}</td>
                                </tr>
                            </tbody>
                        </table> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
