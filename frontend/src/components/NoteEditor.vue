<template>
  <div id="editor">
    <mavon-editor v-model="content" language="en"/>
  </div>
</template>

<script>
export default {
  data () {
    return {
      content: ''
    }
  }
}
</script>

<style lang="less" scoped>
#editor, .v-note-wrapper {
  height: 100%;
}
</style>
