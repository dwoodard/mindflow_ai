<template>
    <div>
        <h2>This is rendered by Vue!</h2>
        <button @click="count++">Click Me</button>
        <p>You've clicked {{ count }} times.</p>
    </div>
</template>

<script>
export default {
    name: 'ExampleComponent',
    data() {
        return {
            count: 0,
        };
    },
};
</script>
