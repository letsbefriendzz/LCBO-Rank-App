<template>
    <SearchBar @search-request="updateAlcohols"></SearchBar>
    <div id="alcohols" v-for="alcohol in response" :key="alcohol.permanent_id">
        <AlcoholCard class="alcohol-card" v-bind:alcohol="alcohol"></AlcoholCard>
    </div>
</template>

<script>
import AlcoholCard from "./AlcoholCard.vue";
import SearchBar from "./SearchBar.vue";
import axios from "axios";
import {debounce} from "lodash";

export default {
    name: 'App',
    data() {
        return {
            response: null,
        }
    },
    methods: {
        updateAlcohols: debounce(
            function (event) {
                axios.get(`http://lcbostats.com/api/alcohol?search=${event.query}`)
                    .then((response) => this.response = response.data.data)
            }, 500, {'leading': true})
    },
    // bind to a url string?
    components: {
        AlcoholCard,
        SearchBar,
    },
    mounted() {
        axios.get(`http://lcbostats.com/api/alcohol?sortBy=price_index`)
            .then((response) => this.response = response.data.data)
    }
}

</script>

<style>
#alcohols {
    display: flex;
    flex-direction: row;
    margin: 1vw;
}
</style>
