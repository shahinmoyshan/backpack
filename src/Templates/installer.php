<!DOCTYPE html>
<html lang="<?= env('lang', 'en') ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __e('Installer') ?></title>

    <!-- Tailwind Global Style Config -->
    <?= tailwind() ?>
</head>

<body class="relative min-h-screen flex items-center justify-center bg-primary-100">

    <!-- Preloader -->
    <?= tailwind()->getPreloaderElement() ?>

    <div id="app" class="w-full max-h-screen overflow-y-auto max-w-3xl mx-auto bg-white shadow-lg relative"
        x-data="installer">
        <div x-cloak x-show="loading"
            class="absolute inset-0 w-full h-full z-20 flex items-center justify-center bg-white/50">
            <svg aria-hidden="true" class="size-6 text-accent-200 animate-spin fill-accent-700" viewBox="0 0 100 101"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor" />
                <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill" />
            </svg>
        </div>
        <div class="bg-accent-700 text-accent-50 px-8 py-6 text-center">
            <h2 class="text-2xl font-semibold mb-1"><?= __e('Installer') ?></h2>
            <p class="text-sm opacity-75"
                x-text="'<?= __e('Step %s/%s', ['{current}', '{total}']) ?>'.replace('{current}', step).replace('{total}', <?= _e(count($steps)) ?>)">
            </p>
        </div>
        <div x-cloak x-show="error"
            class="px-6 py-4 md:py-6 md:px-8 bg-red-50 text-red-800 font-medium flex items-center gap-2 justify-between">
            <p x-text="error"></p>
            <button x-on:click="error=null">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                </svg>
            </button>
        </div>
        <?php foreach ($steps as $key => $step): ?>
            <template x-if="step === <?= $key + 1 ?>">
                <div class="px-6 py-6 md:px-12 md:py-8">
                    <h2 class="text-2xl font-semibold mb-1"><?= _e($step['title']) ?></h2>
                    <p class="text-sm text-primary-800">
                        <?= _e($step['description']) ?>
                    </p>
                    <div class="mt-6">
                        <?php include dir_path($step['template']) ?>
                    </div>
                </div>
            </template>
        <?php endforeach ?>
    </div>
    <script>
        function installer() {
            return {
                step: this.$persist(1).as('saleo_installer_step').using(sessionStorage),
                error: null,
                loading: false,
                init() {
                    this.updateDocTitle();
                    this.$watch('step', () => this.updateDocTitle());
                },
                updateDocTitle() {
                    document.title = '<?= __e('Step %s » Installer', '{step}') ?>'.replace('{step}', this.step);
                },
                nextStep() {
                    if (this.step === <?= _e(count($steps)) ?>) return;
                    this.error = null;
                    this.step++;
                },
                prevStep() {
                    if (this.step === 1) return;
                    this.error = null;
                    this.step--;
                },
                callback(data = {}) {
                    const app = document.getElementById('app');
                    app.style.opacity = 0.5;
                    app.style.pointerEvents = 'none';

                    this.loading = true;
                    this.error = null;

                    fetch('<?= request_url() ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ __installer_step_callback: this.step, ...data })
                    })
                        .then(resp => resp.json())
                        .then(resp => {
                            if (resp.redirect) {
                                window.location.href = resp.redirect;
                            } else if (resp.nextStep) {
                                this.nextStep();
                            } else if (resp.prevStep) {
                                this.prevStep();
                            } else if (resp.step) {
                                this.step = step;
                            } else if (resp.error) {
                                this.error = resp.error;
                            }

                            app.style.opacity = 1;
                            app.style.pointerEvents = 'all';

                            this.loading = false;
                        });
                }
            };
        }
    </script>
</body>

</html>