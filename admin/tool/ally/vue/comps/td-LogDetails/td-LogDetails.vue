<template>
    <div>
        <div>{{value.message}}</div>
        <div v-if="value.data !== null" class="mt-4">
            <button class="btn btn-default" v-on:click="showData = !showData">
                <span v-if="!showData">
                    {{strings.showdata}}
                </span>
                <span v-else>
                    {{strings.hidedata}}
                </span>
            </button>
        </div>

        <div class="mt-4" v-if="showData" v-html="value.data"></div>
        <div v-if="value.explanation !== null" class="mt-4">
            <button class="btn btn-default" v-on:click="showExplanation = !showExplanation">
                <span v-if="!showExplanation">
                    {{strings.showexplanation}}
                </span>
                <span v-else>
                    {{strings.hideexplanation}}
                </span>
            </button>
        </div>

        <div class="mt-4" v-if="showExplanation" v-html="value.explanation"></div>
        <div v-if="value.exception !== null" class="mt-4">
            <button class="btn btn-default" v-on:click="showException = !showException">
                <span v-if="!showException">
                    {{strings.showexception}}
                </span>
                <span v-else>
                    {{strings.hideexception}}
                </span>
            </button>
        </div>
        <div class="mt-4" v-if="showException" v-html="value.exception"></div>
    </div>

</template>
<script>
export default {
    props: ['value', 'showData', 'showExplanation', 'showException'],
    data: function() {
        return {
            strings: {}
        }
    },
    mounted () {
        var self = this;
        self.strings.showdata = 'so';
        requirejs(["core/str"], function(Str) {
            Str.get_strings([
                {key: 'showdata', component: 'tool_ally'},
                {key: 'hidedata', component: 'tool_ally'},
                {key: 'showexplanation', component: 'tool_ally'},
                {key: 'hideexplanation', component: 'tool_ally'},
                {key: 'showexception', component: 'tool_ally'},
                {key: 'hideexception', component: 'tool_ally'}
            ]).then(function(strings) {
                // We need the strings data item to be a new object for change detection to work.
                // Therefore we use the spread operator to add in the strings.
                self.strings = {
                    ...self.strings,
                    showdata: strings[0],
                    hidedata: strings[1],
                    showexplanation: strings[2],
                    hideexplanation: strings[3],
                    showexception: strings[4],
                    hideexception: strings[5]
                };
            });
        });
    },
}
</script>
