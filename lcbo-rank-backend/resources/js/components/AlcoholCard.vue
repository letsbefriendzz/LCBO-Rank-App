<template>
    <div>
        <div id="alcohol-card-wrapper">
            <div id="product-data">
                <h3>{{ alcohol.title }}</h3>
                <p><i>{{ alcohol.brand }} - {{ alcohol.subcategory }}</i></p>
                <p>${{ alcohol.price }} - {{ alcohol.volume }}ml</p>
                <p>{{ alcohol.alcohol_content }}% alc. - ${{ alcohol.price_index.toFixed(2) }}/ml</p>
            </div>
        </div>
        <price-history-graph :alcoholId="alcohol.permanent_id"></price-history-graph>
    </div>
</template>

<script>
import PriceHistoryGraph from "@/components/PriceHistoryGraph.vue";

export default {
    name: "CounterButton",
    data() {
        return {}
    },
    components: {
        PriceHistoryGraph
    },
    props: {
        alcohol: Object,
    },
    mounted() {
        console.log(this.alcohol.id)
    }
}
</script>

<style scoped>
#product-data {
    color: #D4D5EB;
}

#alcohol-card-wrapper {
    width: 20vw;
    padding: 2vh 2vw;
    background-color: #242F40;
}
</style>
