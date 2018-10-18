<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Invoice
                <strong></strong>
                <span class="float-right"> <strong>Status:</strong> Pending</span>

            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h6 class="mb-3">From:</h6>
                        <div>
                            <strong>Webz Poland</strong>
                        </div>
                        <div>Madalinskiego 8</div>
                        <div>71-101 Szczecin, Poland</div>
                        <div>Email: info@webz.com.pl</div>
                        <div>Phone: +48 444 666 3333</div>
                    </div>

                    <div class="col-sm-6">
                        <h6 class="mb-3">To:</h6>
                        <div>
                            <strong>Bob Mart</strong>
                        </div>
                        <div>Attn: Daniel Marek</div>
                        <div>43-190 Mikolow, Poland</div>
                        <div>Email: marek@daniel.com</div>
                        <div>Phone: +48 123 456 789</div>
                    </div>


                </div>

                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>

                            <th class="right">Hours Cost</th>
                            <th class="right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="project in projects">
                            <td class="left">{{project.name}}</td>

                            <td class="center" v-text="price"></td>
                            <td class="right" v-text="totalPrice"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-5">

                    </div>

                    <div class="col-lg-4 col-sm-5 ml-auto">
                        <table class="table table-clear">
                            <tbody>

                            <tr>
                                <td class="left">
                                    <strong>VAT (10%)</strong>
                                </td>
                                <td class="right" v-text="vat">$</td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong>Total</strong>
                                </td>
                                <td class="right" v-text="totalPrice">
                                    <strong>$</strong>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                discount: '',
                hours: '',
                price: '',
                projects: '',
                totalPrice: ''

            }
        },
        mounted() {
            this.$nextTick(() => {
                axios.get('/projects/services').then(({data}) => {
                    console.log(data);
                    this.hours = data['hours'];
                    this.projects = data.projects;
                    this.price = 10;
                    this.totalPrice = this.hours * this.price;
                });
            })
        },
        computed: {
            vat: function () {
                return this.totalPrice* 0.1;
            }
        }
    }
</script>