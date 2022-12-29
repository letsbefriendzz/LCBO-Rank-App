<template>
    <div>
        <Line :data="chartData" :options="options" id="line"/>
    </div>
</template>

<script>
import {CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip} from 'chart.js'
import {Line} from 'vue-chartjs'
import axios from "axios";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

export default {
    name: 'PriceHistoryGraph',
    components: {
        Line
    },
    data() {
        return {
            loaded: false,
            chartData: {
                labels: [],
                datasets: [
                    {
                        label: 'Data One',
                        backgroundColor: '#f87979',
                        data: []
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    },
    methods: {
        getDatesBetween: function (start, end) {
            let dates = []
            start = new Date(Date.parse(start))
            end = new Date(Date.parse(end))

            while (start <= end) {
                dates.push(new Date(start))
                start.setDate(start.getDate() + 1)
            }

            dates.push(new Date(start))

            return dates
        }
    },
    props: {
        alcoholId: Number,
    },
    async mounted() {
        this.loaded = false;

        try {
            const url = `http://lcbostats.com/api/history/${this.alcoholId}`

            axios.get(url).then((response) => {
                const priceChanges = response.data.price_change_history;
                priceChanges[0].created_at
                priceChanges[priceChanges.length - 1].created_at

                this.chartData = {
                    labels: priceChanges.map((priceChange) => new Date(priceChange.created_at).toISOString().split('T')[0]),
                    datasets: [
                        {
                            label: "Price",
                            backgroundColor: '#f87979',
                            data: priceChanges.map((priceChange) => priceChange.price)
                        }
                    ]
                }
            }).catch(() => {
                console.log(url)
            })
            this.loaded = true
        } catch (e) {
            console.log(e)
        }
    }
}
</script>

<style>
#line {
    width: 90vw;
    height: 30vw;
}
</style>
