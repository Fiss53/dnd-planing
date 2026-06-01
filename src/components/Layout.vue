<template>
 <div class="layout">
  <header v-if="$slots.header"><slot name="header"></slot></header>
  <aside v-if="$slots.aside"><slot name="aside"></slot></aside>
  <main v-if="$slots.main"><slot name="main"></slot></main>
  <footer v-if="$slots.footer"><slot name="footer"></slot></footer>
 </div>
</template>

<style>
    .layout {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    main{
        flex: 1;
    }
</style>