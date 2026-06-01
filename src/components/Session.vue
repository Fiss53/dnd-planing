<template>
    <div class="session-card">
        <div class="session-top">
            <div class="content">
                <h1 class="session-card-title">{{ title }}</h1>
                <hr>
                <p class="session-card-info">{{ info }}</p>
                <p class="session-card-game-master">Maître de jeu : {{ gameMaster }}</p>
            </div>
            <img v-if="image" :src="image" :alt="title"/>
            <div v-else class="placeholder">Image</div>
        </div>
        <div class="tags">
            <span
            v-for="tag in tags"
            :key="tag"
            class="tag"
            >
            {{ tag }}
            </span>
        </div>
    </div>
</template>

<script setup>
    defineProps({
        title: String,
        info: String,
        gameMaster: String,
        image: String,
        dates: {type: Array,
        default: () => []},
        tags: {type: Array,
        default: () => []},
        special: Boolean
    })
</script>

<style>
    .content {
        flex: 1;
        text-align: left;
        padding-right: 30px;
    }

    .session-card{
        display: flex;
        flex-direction: column;
        background: #dfdfdf;
        width: clamp(320px, 85vw, 900px);
        border-radius: 24px;
        padding: 12px;
        margin: 30px auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35);
        border: 1px solid rgba(123, 123, 123, 0.5);
        box-sizing: border-box;
    }

    .session-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
    }

    .session-card-title{
        margin: 2px 0;
    }

    .session-card-info{
        margin: 10px 0;
    }

    .session-card-game-master{
        margin: 10px 0;
    }

    img{
        width: clamp(120px, 20vw, 250px);
        max-width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 16px;
        flex-shrink: 0;
    }

    .tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    }

    .tag {
    background: #444;
    color: white;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.8rem;
    }
</style>