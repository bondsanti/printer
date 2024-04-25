<template>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">ปริมาณการพิมพ์ของเครื่องในแต่ละเดือน</h5>
                <div class="form-check form-check-inline">
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="fuji24"
                            v-model="selectedPrinters"
                            value="Fuji24"
                            @change="filterDataBar"
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
                            v-model="selectedPrinters"
                            value="Fuji25"
                            @change="filterDataBar"
                        />
                        <label class="form-check-label" for="fuji25"
                            >Fuji25</label
                        >
                    </div>
                </div>
                <div class="">
                <canvas
                    ref="chartCanvas"
                ></canvas>
            </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">..</h5>

            </div>

        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Chart from "chart.js/auto";
import axios from "axios";

const chartCanvas = ref(null);
const chart = ref(null);
const selectedPrinters = ref(["Fuji24", "Fuji25"]);

const thaiMonths = {
    1: "มกราคม",
    2: "กุมภาพันธ์",
    3: "มีนาคม",
    4: "เมษายน",
    5: "พฤษภาคม",
    6: "มิถุนายน",
    7: "กรกฎาคม",
    8: "สิงหาคม",
    9: "กันยายน",
    10: "ตุลาคม",
    11: "พฤศจิกายน",
    12: "ธันวาคม",
};

onMounted(async () => {
    await Promise.all([fetchDataAndRenderChart()]);
});

async function fetchDataAndRenderChart() {
    try {
        const response = await axios.get("/api/data/chart", {
            params: {
                printers: selectedPrinters.value, // ส่งเครื่องพิมพ์ที่เลือกไปให้ API
            },
        });
        const data = response.data.data;
        renderChart(data);
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

function renderChart(data) {
    if (chart.value) {
        chart.value.destroy();
    }

    const thaiMonthsLabels = data.map((item) => thaiMonths[item.month]);
    const totalColors = data.map((item) => item.total_color);
    const totalBW = data.map((item) => item.total_bw);

    // bar chart
    chart.value = new Chart(chartCanvas.value.getContext("2d"), {
        type: "bar",
        data: {
            labels: thaiMonthsLabels,
            datasets: [
                {
                    label: "Total Color",
                    data: totalColors,
                    backgroundColor: "rgba(255, 99, 132, 0.2)",
                    borderColor: "rgba(255, 99, 132, 1)",
                    borderWidth: 1,
                },
                {
                    label: "Total BW",
                    data: totalBW,
                    backgroundColor: "rgba(54, 162, 235, 0.2)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

async function filterDataBar() {
    await fetchDataAndRenderChart();
}
</script>
<style>
.chart-container {
    height:40vh;
  width:80vw;
}
</style>
