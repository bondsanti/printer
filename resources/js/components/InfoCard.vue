<style scoped>
/* สีพื้นหลังของการ์ด */
.card {
    background-color: #f8f9fa; /* เปลี่ยนสีพื้นหลังตามต้องการ */
}

/* สีขอบของการ์ด */
.card {
    border-color: #dee2e6; /* เปลี่ยนสีขอบตามต้องการ */
}

/* สีข้อความหัวข้อ */
.card-title {
    color: #007bff; /* เปลี่ยนสีข้อความหัวข้อตามต้องการ */
}

/* ขนาดและสีข้อความ */
.card-text {
    font-size: 16px; /* เปลี่ยนขนาดข้อความตามต้องการ */
    color: #333; /* เปลี่ยนสีข้อความตามต้องการ */
}

/* ข้อความหัวข้อในแต่ละคอลัมน์ */
.col-6 h6 {
    font-size: 18px; /* เปลี่ยนขนาดข้อความตามต้องการ */
    color: #555; /* เปลี่ยนสีข้อความตามต้องการ */
}

/* ข้อความในแต่ละคอลัมน์ */
.col-6 p {
    font-size: 16px; /* เปลี่ยนขนาดข้อความตามต้องการ */
    color: #777; /* เปลี่ยนสีข้อความตามต้องการ */
}

/* เส้นขั้นเว้น */
hr.my-4 {
    border-top: 1px solid #ddd; /* เปลี่ยนสีของเส้นขั้นเว้นตามต้องการ */
}

</style>
<template>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <h5 class="card-title mb-4">จำนวนพิมพ์ ขาวดำ</h5>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 24</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 25</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>
                            <!-- เพิ่มเพื่อขึ้นบรรทัดใหม่ -->
                            <div class="col-12">
                                <hr class="my-4" />
                                <!-- เพิ่มเส้นขั้นเว้น -->
                                <h5 class="card-title mb-4">รวม</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <h5 class="card-title mb-4">จำนวนพิมพ์ สี</h5>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 24</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 25</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>

                            <!-- เพิ่มเพื่อขึ้นบรรทัดใหม่ -->
                            <div class="col-12">
                                <hr class="my-4" />
                                <!-- เพิ่มเส้นขั้นเว้น -->
                                <h5 class="card-title mb-4">รวม</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <h5 class="card-title mb-4">รวม</h5>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 24</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>
                            <div class="col-6 mb-4">
                                <h6 class="mb-2">เครื่องพิมพ์ ชั้น 25</h6>
                                <p class="mb-0">1000 แผ่น</p>
                            </div>

                            <!-- เพิ่มเพื่อขึ้นบรรทัดใหม่ -->
                            <div class="col-12">
                                <hr class="my-4" />
                                <!-- เพิ่มเส้นขั้นเว้น -->
                                <h5 class="card-title mb-4">รวม</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
</template>
