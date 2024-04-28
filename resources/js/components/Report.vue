<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import Swal from "sweetalert2";
import Loading from "../components/Loading.vue";

const items = ref([]);
const departments = ref([]);
const selectedDepartment = ref(null);
const users = ref([]);
const selectedUsers = ref(null);
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

    // console.log(startDate.value);

    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data", {
            params: {
                startDate: startDate.value,
                endDate: endDate.value,
            },
        });
        //console.log(response);
        isLoading.value = false;
        items.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }

    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/department", {});
        console.log(response);
        //isLoading.value = false;
        departments.value = response.data.data;
        //console.log(items.value);
    } catch (error) {
        console.log(error);
    }

    try {
        isLoading.value = true;
        const response = await axios.get("/api/report/data/user", {});
        //console.log(response);
        isLoading.value = false;
        users.value = response.data.data;
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

function submitForm() {
    // ดำเนินการ filter ข้อมูลตามวันที่ที่เลือก
    // ใช้ startDate.value และ endDate.value ในการค้นหาข้อมูล
}
</script>

<template>
    <div class="mt-3">
        <div class="row">
            <!-- <div class="col-md-6 offset-md-3"> -->
                <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="row g-3" @submit.prevent="submitForm">
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
                            <div class="col-md-6">
                                <label for="" class="form-label"
                                    >เลือกแผนก</label
                                >
                                <select
                                    class="form-select"
                                    id="department"
                                    name="department"
                                    aria-label="Default select example"
                                    v-model="selectedDepartment"
                                    multiple
                                >
                                    <option value="">เลือก</option>
                                    <option
                                        v-for="department in departments"
                                        :key="department.department_id"
                                        :value="department.department_id"
                                    >
                                        {{ department.department_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="form-label"
                                    >เลือกผู้ใช้</label
                                >
                                <select
                                    class="form-select"
                                    id="user"
                                    name="user"
                                    v-model="selectedDepartment"
                                    multiple
                                    aria-label="Default select example"
                                >
                                <option value="">เลือก</option>
                                    <option
                                        v-for="user in users"
                                        :key="user.code"
                                        :value="user.code"
                                    >
                                        {{ user.user }}
                                    </option>
                                </select>
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
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">ปริมาณการพิมพ์แต่ละเครื่อง</h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_color"

                                    value="total_color"
                                />
                                <label class="form-check-label" for="color"
                                    >Total Color</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_bw"

                                    value="total_bw"

                                />
                                <label class="form-check-label" for="bw"
                                    >Total BW</label
                                >
                            </div>
                        </div>
                        <div
                            id=""
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            ปริมาณการใช้งานรวมแต่ละแผนก
                        </h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji24"

                                    value="Fuji24"

                                />
                                <label class="form-check-label" for="fuji24"
                                    >Fuji24</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="fuji25"

                                    value="Fuji25"

                                />
                                <label class="form-check-label" for="fuji25"
                                    >Fuji25</label
                                >
                            </div>
                        </div>
                        <div
                            id=""
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">ปริมาณการพิมพ์ของผู้ใช้งาน</h5>
                        <div class="form-check form-check-inline">
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_color"

                                    value="total_color"
                                />
                                <label class="form-check-label" for="color"
                                    >Total Color</label
                                >
                            </div>
                            <div class="form-check form-check-inline">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="total_bw"

                                    value="total_bw"

                                />
                                <label class="form-check-label" for="bw"
                                    >Total BW</label
                                >
                            </div>
                        </div>
                        <div
                            id=""
                            style="
                                min-width: 310px;
                                height: 400px;
                                margin: 0 auto;
                            "
                        ></div>
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
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">แผนก</th>
                                        <th scope="col">รหัสพนักงาน</th>
                                        <th scope="col">ชื่อ-สกุล</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">ขาวดำ</th>
                                        <th scope="col">รวม</th>
                                    </tr>
                                </thead>
                                <tbody v-if="isLoading">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <loading></loading>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-if="!isLoading">
                                    <!-- แสดงข้อความ "No data available" ถ้าไม่มีข้อมูล -->
                                    <tr v-if="items.length === 0">
                                        <td colspan="7" class="text-center">
                                            No data available
                                        </td>
                                    </tr>
                                    <!-- วนลูปผ่านแต่ละแผนก -->
                                    <template
                                        v-for="(department, index) in items"
                                        :key="index"
                                    >
                                        <tr>
                                            <td
                                                colspan=""
                                                class="text-center"
                                                style="
                                                    background-color: #bfdbfe;
                                                "
                                            >
                                                {{ department.department_name }}
                                            </td>
                                            <td
                                                colspan="5"
                                                class="text-center"
                                                style="
                                                    background-color: #bfdbfe;
                                                "
                                            ></td>
                                        </tr>
                                        <!-- วนลูปผ่านผู้ใช้งานในแผนก -->
                                        <tr
                                            v-for="(
                                                user, userIndex
                                            ) in department.users"
                                            :key="'user-' + userIndex"
                                        >
                                            <td></td>
                                            <td>{{ user.code }}</td>
                                            <td>{{ user.name }}</td>
                                            <td>{{ user.total_bw }}</td>
                                            <td>{{ user.total_color }}</td>
                                            <td>{{ user.total }}</td>
                                        </tr>
                                        <!-- แสดงผลรวมของแผนก -->
                                        <tr style="background-color: #93c5fd">
                                            <td colspan="3"></td>
                                            <!-- <td class="text-right">Total department</td> -->
                                            <td>{{ department.total_bw }}</td>
                                            <td>
                                                {{ department.total_color }}
                                            </td>
                                            <td>{{ department.total }}</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
