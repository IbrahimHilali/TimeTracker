<template>
    <div class="modal fade" id="uploadFilesModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="upload">
                        <div class="upload__header">
                            <slot name="header">File(s) Upload</slot>
                        </div>

                        <p class="btn-group">
                            <button type="button" class="btn btn-default"
                                    v-on:click="flowOpenFileDialog()">
                                <span class="glyphicon glyphicon-file"></span>
                            </button>
                        </p>

                        <input ref="flowFileInput" type="file" style="visibility: hidden; position: absolute;">

                        <ul class="upload__file-list" v-show="files.length > 0">
                            <li v-for="file in files" class="upload__file-list__item">
                                {{ file.name }}
                                <span class="upload__file-list__item__size">
                    {{ Math.round(file.size / 1024 * 10) / 10 }}KB
                </span>

                                <div class="progress" v-show="uploadRunning">
                                    <div :class="progressBarClass(file)" role="progressbar" aria-valuenow="0"
                                         aria-valuemin="0"
                                         aria-valuemax="100"
                                         :style="progressBarStyle(file)">
                                        {{ file.progress }}%
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <slot name="footer"></slot>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" v-on:click="flowStartUpload()"
                            v-show="fileSelected" :disabled="uploadCompleted">Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<style>
    .upload {
        background-color: #e9e9e9;
        padding: 10px;
    }

    .upload__file-list__item__size {
        font-size: 8pt;
        font-style: italic;
    }
</style>

<script>
    import Flow from '@flowjs/flow.js';

    export default {
        props: ['target'],

        data() {
            return {
                showModal:false,
                flow: null,
                files: [],
                fileSelected: false,
                uploadRunning: false,
                uploadCompleted: false,
            }
        },

        mounted() {

            this.flowInitialize();

            this.flowBindEvents();
        },

        methods: {
            flowInitialize() {
                this.flow = new Flow({
                    target: '/FilesManger/upload',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    query: {}
                });

                this.flow.assignBrowse(this.$refs.flowFileInput, false, false, {});
            },

            flowBindEvents() {
                this.flow.on('fileAdded', (file, event) => {
                    this.fileSelected = true;
                    this.files.push({
                        name: file.name,
                        size: file.size,
                        progress: 0,
                        state: 'running',
                    });
                });

                this.flow.on('fileProgress', (file, event) => {
                    this.uploadRunning = true;

                    this.files.forEach(item => {
                        if (item.name === file.name) {
                            item.progress = Math.round(file.progress() * 10000) / 100;
                        }
                    });
                });

                this.flow.on('fileSuccess', (file, message) => {
                    this.uploadCompleted = true;

                    this.files.forEach(item => {
                        if (item.name === file.name) {
                            item.state = 'complete';
                        }
                    });

                    let completed = true;

                    this.files.forEach(item => {
                        if (item.state !== 'complete') {
                            completed = false;
                        }
                    });

                    if (completed) {
                        this.$emit('complete');

                        setTimeout(() => {
                            this.flowReset();
                        }, 2500);
                    }
                });

                this.flow.on('fileError', (file, message) => {
                    this.uploadCompleted = false;

                    this.files.forEach(item => {
                        if (item.name === file.name) {
                            item.state = 'error';
                        }
                    });
                });
            },

            flowOpenFileDialog() {
                $(this.$refs.flowFileInput).click();
            },

            flowStartUpload() {
                this.flow.upload();
            },

            flowReset() {
                this.files = [];
                this.fileSelected = false;
                this.uploadRunning = false;
                this.uploadCompleted = false;

                this.flow.cancel();
            },

            progressBarStyle(file) {
                return "width: " + file.progress + "%; min-width: 2em;";
            },

            progressBarClass(file) {
                if (file.state === 'error') {
                    return "progress-bar progress-bar-danger";
                }

                if (file.state === 'complete') {
                    return "progress-bar progress-bar-success";
                }

                return "progress-bar";
            }
        }
    }
</script>