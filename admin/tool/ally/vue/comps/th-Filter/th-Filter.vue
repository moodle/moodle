<template>
  <div class="btn-group">
    {{ title }}
    <!-- TODO, localise "Filter for" -->
    <a href="javascript:;" data-toggle="dropdown" role="button" v-bind:aria-label="'Filter for ' + title">
      <i class="fa fa-filter" :class="{ 'text-muted': !keyword }"></i>
    </a>
    <ul class="dropdown-menu" style="padding: 3px">
      <div class="input-group input-group-sm">
        <input type="search" class="form-control" ref="input"
          v-model="keyword" v-on:click="searchclick" @keydown.enter="search" :placeholder="`Search ${field}...`">
          <span class="input-group-btn">
            <button class="btn btn-default fa fa-search" @click="search"></button>
          </span>
      </div>
    </ul>
  </div>
</template>
<script>
export default {
  props: ['field', 'title', 'query'],
  data: () => ({
    keyword: ''
  }),
  mounted () {
    $(this.$el).on('shown.bs.dropdown', e => this.$refs.input.focus())
  },
  watch: {
    keyword (kw) {
      // reset immediately if empty
      if (kw === '') this.search()
    }
  },
  methods: {
    searchclick: function(e) {
        e.stopPropagation();
    },
    search () {
      const { query } = this
      // `$props.query` would be initialized to `{ limit: 10, offset: 0, sort: '', order: '' }` by default
      // custom query conditions must be set to observable by using `Vue.set / $vm.$set`
      this.$set(query, 'filter', this.field + '~' + this.keyword); // GT mod - use a filter field for filters.
      query.offset = 0 // reset pagination
    }
  }
}
</script>
<style>
input[type=search]::-webkit-search-cancel-button {
  -webkit-appearance: searchfield-cancel-button;
  cursor: pointer;
}
</style>
