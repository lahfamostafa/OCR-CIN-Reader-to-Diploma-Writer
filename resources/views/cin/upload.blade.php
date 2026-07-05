<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouveau diplôme — Scanner CIN') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash />

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r">
                    <p class="text-sm font-medium text-indigo-900"
                       x-show="currentStep === 'recto' && !capturedImageRecto && !isTransitioning">
                        📸 <strong>Étape 1 : RECTO</strong> — Placez le DEVANT de la carte dans le cadre.
                    </p>
                    <p class="text-sm font-medium text-blue-900"
                       x-show="isTransitioning">
                        🔄 <strong>Transition</strong> — Retournez la carte, la détection reprend dans <span x-text="transitionSecondsLeft"></span>s…
                    </p>
                    <p class="text-sm font-medium text-amber-900"
                       x-show="currentStep === 'verso' && !capturedImageVerso && !isTransitioning">
                        📸 <strong>Étape 2 : VERSO</strong> — Retournez la carte et placez le DOS dans le cadre.
                    </p>
                    <p class="text-sm font-medium text-green-900"
                       x-show="capturedImageRecto && capturedImageVerso">
                        ✅ Les deux faces ont été capturées avec succès !
                    </p>
                </div>

                <form method="POST" action="{{ route('cin.store') }}" enctype="multipart/form-data"
                    x-data="cinScanner()" @submit.prevent="submitForm()">
                    @csrf

                    <input type="file" name="document_recto" accept="image/*" class="hidden" x-ref="file">
                    <input type="file" name="document_verso" accept="image/*" class="hidden" x-ref="fileVerso">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de document</label>
                        <select name="document_type" x-model="documentType" @change="updateContainer()"
                            class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="cin">CIN (Carte)</option>
                            <option value="passport">Passeport</option>
                        </select>
                    </div>

                    {{-- Viewfinder : visible pendant la capture recto OU verso, ET pendant la transition --}}
                    <div class="relative mx-auto bg-black rounded-lg overflow-hidden"
                        :style="containerStyle"
                        x-show="(currentStep === 'recto' && !capturedImageRecto) || (currentStep === 'verso' && !capturedImageVerso) || isTransitioning">

                        <video id="scanner-view" autoplay playsinline muted
                            class="absolute inset-0 w-full h-full object-cover"
                            x-show="cameraActive"></video>

                        {{-- Zones de texte détectées --}}
                        <div class="absolute inset-0 pointer-events-none" x-show="cameraActive && !isTransitioning">
                            <template x-for="zone in textZones" :key="zone.key">
                                <div class="absolute border"
                                     :style="`left:${zone.x}px; top:${zone.y}px; width:${zone.w}px; height:${zone.h}px; background-color:${zone.color}; opacity:0.22; border-color:${zone.color};`"></div>
                            </template>
                            <div x-show="contourDetected" class="absolute border-2 border-dashed border-emerald-400"
                                 :style="`left:${contourBox.x}px; top:${contourBox.y}px; width:${contourBox.w}px; height:${contourBox.h}px;`"></div>
                        </div>

                        {{-- Flash blanc --}}
                        <div class="absolute inset-0 bg-white pointer-events-none transition-opacity duration-150"
                            :class="flashActive ? 'opacity-100' : 'opacity-0'"></div>

                        {{-- ======================================================
                             OVERLAY DE TRANSITION — piloté par Alpine (pas de DOM manuel)
                             ====================================================== --}}
                        <template x-if="isTransitioning">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/70 z-10">
                                <div class="text-center p-6 bg-white/10 backdrop-blur-sm rounded-xl">
                                    <p class="text-white text-lg font-semibold">Retournez la carte…</p>
                                    <p class="text-white text-4xl font-bold mt-2" x-text="transitionSecondsLeft + 's'"></p>
                                    <button type="button"
                                            @click="forceEndTransition()"
                                            class="mt-4 px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-medium rounded-lg transition">
                                        Prêt, capturer le verso maintenant
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- Bandeaux de statut (hors transition) --}}
                        <template x-if="cameraActive && !isTransitioning">
                            <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex flex-col items-center gap-1">
                                <template x-if="isTooDark">
                                    <div class="bg-yellow-600/90 text-white text-xs px-3 py-1 rounded-full">Ajoutez de la lumière</div>
                                </template>
                                <template x-if="!isTooDark && isBlurry">
                                    <div class="bg-orange-600/90 text-white text-xs px-3 py-1 rounded-full">Image floue - tenez stable</div>
                                </template>
                                <template x-if="!isTooDark && !isBlurry && isMoving">
                                    <div class="bg-black/60 text-white text-xs px-3 py-1 rounded-full">Stabilisation...</div>
                                </template>
                                <template x-if="!isTooDark && !isBlurry && !isMoving">
                                    <div class="bg-green-600/90 text-white text-xs px-3 py-1 rounded-full">
                                        Capture <span x-text="currentStep.toUpperCase()"></span> imminente…
                                    </div>
                                </template>
                            </div>
                        </template>

                        {{-- Écran d'accueil (caméra inactive) --}}
                        <template x-if="!cameraActive && !isTransitioning">
                            <div class="absolute inset-0 flex items-center justify-center cursor-pointer hover:bg-white/5 transition"
                                @click="startCamera()" role="button">
                                <template x-if="!cameraError">
                                    <div class="text-center px-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        </svg>
                                        <p class="mt-3 text-sm font-medium text-gray-200">Démarrer le scanner (<span x-text="currentStep"></span>)</p>
                                    </div>
                                </template>
                                <template x-if="cameraError">
                                    <div class="text-center px-4">
                                        <svg class="mx-auto h-10 w-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-3 text-sm font-medium text-red-300" x-text="cameraError"></p>
                                        <p class="mt-1 text-xs text-gray-400">Cliquez pour réessayer</p>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Aperçus recto / verso --}}
                    <div class="grid grid-cols-2 gap-4 mt-4" x-show="capturedImageRecto || capturedImageVerso">
                        <div x-show="capturedImageRecto" class="relative bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-gray-700">Aperçu du Recto</span>
                                <button type="button" @click="deleteRecto()"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                                    Supprimer
                                </button>
                            </div>
                            <img :src="capturedImageRecto" class="w-full h-auto rounded border border-gray-300">
                        </div>

                        <div x-show="capturedImageVerso" class="relative bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-gray-700">Aperçu du Verso</span>
                                <button type="button" @click="deleteVerso()"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500">
                                    Supprimer
                                </button>
                            </div>
                            <img :src="capturedImageVerso" class="w-full h-auto rounded border border-gray-300">
                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            {{-- Bascule caméra avant/arrière — masqué en transition --}}
                            <template x-if="cameraActive && !isTransitioning && ((currentStep === 'recto' && !capturedImageRecto) || (currentStep === 'verso' && !capturedImageVerso))">
                                <button type="button" @click="toggleCamera()"
                                        class="p-3 bg-gray-700 text-white rounded-full hover:bg-gray-800 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </template>

                            {{-- Capture manuelle — disponible hors transition (recto ou verso non encore capturé) --}}
                            <template x-if="cameraActive && !isTransitioning && ((currentStep === 'recto' && !capturedImageRecto) || (currentStep === 'verso' && !capturedImageVerso))">
                                <button type="button" @click="captureManual()"
                                        class="px-5 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                                    Capturer <span x-text="currentStep"></span> maintenant
                                </button>
                            </template>

                            <template x-if="capturedImageRecto || capturedImageVerso">
                                <button type="button" @click="resetAll()"
                                        class="px-5 py-3 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition">
                                    Tout recommencer
                                </button>
                            </template>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 disabled:opacity-50 transition"
                                :disabled="loading || !fileReady">
                            <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="loading ? 'Analyse OCR en cours…' : 'Valider et analyser'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <canvas id="processing-canvas" class="hidden"></canvas>

    <script>
    function cinScanner() {
        return {
            // ── État général ──────────────────────────────────────────────
            currentStep: 'recto',
            cameraActive: false,
            cameraError: null,
            capturedImageRecto: null,
            capturedImageVerso: null,
            loading: false,
            fileReady: false,
            documentType: 'cin',
            facingMode: 'environment',
            stream: null,
            detectionInterval: null,
            flashActive: false,

            // ── Détection de qualité ──────────────────────────────────────
            isTooDark: false,
            isMoving: false,
            isBlurry: false,
            previousImageData: null,
            requiredStableFrames: 6,
            movementThreshold: 22,
            blurThreshold: 22,
            textZones: [],
            textZonesDetected: false,
            textDetectionThreshold: 14,
            combinedStableFrameCount: 0,

            // ── Transition recto → verso ──────────────────────────────────
            // isTransitioning : true pendant le compte à rebours.
            // Pendant ce temps, checkFrame() retourne immédiatement sans rien faire.
            isTransitioning: false,
            transitionSecondsLeft: 0,
            transitionTimer: null,          // setInterval du compte à rebours
            TRANSITION_DELAY_S: 5,          // secondes de délai

            // ── Contour détecté ───────────────────────────────────────────
            contourDetected: false,
            contourBox: { x: 0, y: 0, w: 0, h: 0 },

            // ── Dimensions d'affichage ────────────────────────────────────
            displayWidth: 640,
            displayHeight: 400,
            containerStyle: '',

            // ─────────────────────────────────────────────────────────────
            init() {
                this.updateContainer();
            },

            updateContainer() {
                const ratio = this.documentType === 'passport' ? 1.42 : 1.586;
                this.containerStyle = `width: 100%; max-width: 520px; aspect-ratio: ${ratio};`;
            },

            // ── Caméra ────────────────────────────────────────────────────
            async startCamera() {
                this.cameraError = null;
                try {
                    const constraints = {
                        video: { facingMode: this.facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } }
                    };
                    this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                    const video = document.getElementById('scanner-view');
                    if (!video) throw new Error('Élément vidéo non trouvé dans le DOM');
                    video.srcObject = this.stream;
                    await video.play().catch(() => {});
                    this.cameraActive = true;
                    this.$nextTick(() => {
                        const container = video.parentElement;
                        if (container) {
                            this.displayWidth = container.clientWidth || 640;
                            this.displayHeight = container.clientHeight || 400;
                        }
                    });
                    this.startAutoDetection();
                } catch (error) {
                    console.error('Camera error:', error);
                    this.cameraActive = false;
                    this.cameraError = "Impossible d'accéder à la caméra.";
                }
            },

            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                    this.stream = null;
                }
                if (this.detectionInterval) {
                    clearInterval(this.detectionInterval);
                    this.detectionInterval = null;
                }
                this.cameraActive = false;
                this._resetDetectionState();
            },

            toggleCamera() {
                this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
                this.stopCamera();
                this.startCamera();
            },

            // Réinitialise toutes les variables de détection de qualité.
            // Appelé après la transition pour repartir sur une frame propre.
            _resetDetectionState() {
                this.combinedStableFrameCount = 0;
                this.previousImageData = null;     // ← pas de comparaison avec l'ancienne frame du recto
                this.isTooDark = false;
                this.isMoving = false;
                this.isBlurry = false;
                this.contourDetected = false;
                this.textZonesDetected = false;
                this.textZones = [];
            },

            // ── Détection automatique ─────────────────────────────────────
            startAutoDetection() {
                this.detectionInterval = setInterval(() => {
                    // Bloc principal : sortir dès que la capture n'est pas attendue
                    if (!this.cameraActive)                                          return;
                    if (this.isTransitioning)                                        return; // ← verrou transition
                    if (this.currentStep === 'recto'  && this.capturedImageRecto)   return;
                    if (this.currentStep === 'verso'  && this.capturedImageVerso)    return;
                    this.checkFrame();
                }, 200);
            },

            checkFrame() {
                const video = document.getElementById('scanner-view');
                if (!video || video.readyState !== 4) return;

                const canvas = document.getElementById('processing-canvas');
                const ctx = canvas.getContext('2d', { willReadFrequently: true });
                const W = 160, H = 120;
                canvas.width = W; canvas.height = H;
                ctx.drawImage(video, 0, 0, W, H);

                const imageData = ctx.getImageData(0, 0, W, H);
                const data = imageData.data;
                const pixelCount = W * H;
                const gray = new Float32Array(pixelCount);
                let totalLum = 0;

                for (let i = 0, p = 0; i < data.length; i += 4, p++) {
                    const lum = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
                    gray[p] = lum;
                    totalLum += lum;
                }

                const avgLum = totalLum / pixelCount;
                this.isTooDark = avgLum < 35;

                // Mouvement : comparaison avec la frame précédente
                // previousImageData est null après la transition → on ne marque pas comme "moving"
                if (this.previousImageData) {
                    let totalDiff = 0;
                    for (let p = 0; p < pixelCount; p++) totalDiff += Math.abs(gray[p] - this.previousImageData[p]);
                    this.isMoving = (totalDiff / pixelCount) > this.movementThreshold;
                } else {
                    this.isMoving = false;
                }
                this.previousImageData = gray;

                // Netteté
                const lapVariance = this.computeLaplacianVariance(gray, W, H);
                this.isBlurry = lapVariance < this.blurThreshold;

                // Zones de texte
                const detectedZones = this.detectTextZones(gray, W, H);
                this.renderTextZones(detectedZones);

                // Contour
                const edgeMask = new Uint8Array(pixelCount);
                let edgePixelCount = 0;
                for (let y = 1; y < H - 1; y++) {
                    for (let x = 1; x < W - 1; x++) {
                        const idx = y * W + x;
                        const gx = gray[idx + 1] - gray[idx - 1];
                        const gy = gray[idx + W] - gray[idx - W];
                        if (Math.sqrt(gx * gx + gy * gy) > 35) { edgeMask[idx] = 1; edgePixelCount++; }
                    }
                }
                if (edgePixelCount > 80) {
                    let minX = W, maxX = 0, minY = H, maxY = 0;
                    for (let y = 0; y < H; y++) {
                        for (let x = 0; x < W; x++) {
                            if (edgeMask[y * W + x]) {
                                if (x < minX) minX = x; if (x > maxX) maxX = x;
                                if (y < minY) minY = y; if (y > maxY) maxY = y;
                            }
                        }
                    }
                    const boxW = maxX - minX, boxH = maxY - minY;
                    const coverage = (boxW * boxH) / pixelCount;
                    if (coverage > 0.15 && coverage < 0.98) {
                        this.contourDetected = true;
                        this.contourBox = {
                            x: (minX / W) * this.displayWidth,
                            y: (minY / H) * this.displayHeight,
                            w: ((maxX - minX) / W) * this.displayWidth,
                            h: ((maxY - minY) / H) * this.displayHeight,
                        };
                    } else { this.contourDetected = false; }
                } else { this.contourDetected = false; }

                // Auto-capture si stable + texte détecté
                const frameIsStable = !this.isTooDark && !this.isMoving && !this.isBlurry;
                if (frameIsStable && this.textZonesDetected) {
                    this.combinedStableFrameCount++;
                    if (this.combinedStableFrameCount >= this.requiredStableFrames) this.captureAuto();
                } else {
                    this.combinedStableFrameCount = 0;
                }
            },

            // ── Capture ───────────────────────────────────────────────────
            captureAuto()   { this.captureImage(); },
            captureManual() { this.captureImage(); },

            captureImage() {
                const video = document.getElementById('scanner-view');
                if (!video || video.readyState !== 4) return;

                this.flashActive = true;
                setTimeout(() => this.flashActive = false, 150);

                const vW = video.videoWidth, vH = video.videoHeight, vRatio = vW / vH;
                const targetRatio = this.documentType === 'passport' ? 1.42 : 1.586;
                let srcW, srcH, srcX, srcY;
                if (vRatio > targetRatio) {
                    srcH = vH; srcW = vH * targetRatio; srcX = (vW - srcW) / 2; srcY = 0;
                } else {
                    srcW = vW; srcH = vW / targetRatio; srcX = 0; srcY = (vH - srcH) / 2;
                }

                const cvs = document.createElement('canvas');
                cvs.width = srcW; cvs.height = srcH;
                cvs.getContext('2d').drawImage(video, srcX, srcY, srcW, srcH, 0, 0, srcW, srcH);

                cvs.toBlob((blob) => {
                    if (!blob) return;
                    const file = new File([blob], `cin_${this.currentStep}.jpg`, { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(file);

                    if (this.currentStep === 'recto') {
                        this.capturedImageRecto = URL.createObjectURL(blob);
                        this.$refs.file.files = dt.files;

                        if (this.documentType === 'passport') {
                            // Passeport : une seule face, on termine ici
                            this.fileReady = true;
                            this.stopCamera();
                        } else {
                            // CIN : lancer le compte à rebours avant le verso
                            this.startTransitionDelay();
                        }
                    } else {
                        // Verso capturé
                        this.capturedImageVerso = URL.createObjectURL(blob);
                        this.$refs.fileVerso.files = dt.files;
                        this.fileReady = true;
                        this.stopCamera();
                    }
                }, 'image/jpeg', 0.95);
            },

            // ── Transition recto → verso ──────────────────────────────────
            startTransitionDelay() {
                // Suspendre la détection automatique via le drapeau
                this.isTransitioning = true;
                this.transitionSecondsLeft = this.TRANSITION_DELAY_S;

                // Compte à rebours réactif (Alpine met à jour le DOM automatiquement)
                this.transitionTimer = setInterval(() => {
                    this.transitionSecondsLeft--;
                    if (this.transitionSecondsLeft <= 0) {
                        this._activateVerso();
                    }
                }, 1000);
            },

            // Bouton "Prêt" dans l'overlay : forcer la fin de transition immédiatement
            forceEndTransition() {
                if (this.transitionTimer) {
                    clearInterval(this.transitionTimer);
                    this.transitionTimer = null;
                }
                this._activateVerso();
            },

            // Passage effectif en mode verso : reset complet de l'état de détection
            _activateVerso() {
                if (this.transitionTimer) {
                    clearInterval(this.transitionTimer);
                    this.transitionTimer = null;
                }
                this.isTransitioning = false;
                this.currentStep = 'verso';
                // ← reset APRÈS avoir passé isTransitioning à false pour que
                //   checkFrame() reparte bien sur une frame propre au prochain tick
                this._resetDetectionState();
            },

            // ── Reset / suppression ───────────────────────────────────────
            resetAll() {
                // Annuler une transition en cours si l'utilisateur repart de zéro
                if (this.transitionTimer) { clearInterval(this.transitionTimer); this.transitionTimer = null; }
                this.isTransitioning = false;
                this.currentStep = 'recto';
                this.capturedImageRecto = null;
                this.capturedImageVerso = null;
                this.fileReady = false;
                const empty = new DataTransfer();
                this.$refs.file.files = empty.files;
                if (this.$refs.fileVerso) this.$refs.fileVerso.files = empty.files;
                this.stopCamera();
                this.startCamera();
            },

            deleteRecto() {
                if (this.transitionTimer) { clearInterval(this.transitionTimer); this.transitionTimer = null; }
                this.isTransitioning = false;
                this.capturedImageRecto = null;
                this.currentStep = 'recto';
                const empty = new DataTransfer();
                this.$refs.file.files = empty.files;
                this.fileReady = false;
                this.stopCamera();
                this.startCamera();
            },

            deleteVerso() {
                this.capturedImageVerso = null;
                this.currentStep = 'verso';
                const empty = new DataTransfer();
                if (this.$refs.fileVerso) this.$refs.fileVerso.files = empty.files;
                // fileReady reste false si recto seul n'est pas suffisant pour CIN
                this.fileReady = this.documentType === 'passport' && !!this.capturedImageRecto;
                this.stopCamera();
                this.startCamera();
            },

            // ── Soumission ────────────────────────────────────────────────
            submitForm() {
                if (!this.fileReady) { alert('Veuillez capturer les faces demandées.'); return; }
                this.loading = true;
                HTMLFormElement.prototype.submit.call(this.$el);
            },

            // ── Utilitaires image ─────────────────────────────────────────
            computeLaplacianVariance(gray, W, H) {
                let sum = 0, sumSq = 0, count = 0;
                for (let y = 1; y < H - 1; y++) {
                    for (let x = 1; x < W - 1; x++) {
                        const idx = y * W + x;
                        const lap = gray[idx - W] + gray[idx + W] + gray[idx - 1] + gray[idx + 1] - 4 * gray[idx];
                        sum += lap; sumSq += lap * lap; count++;
                    }
                }
                const mean = sum / count;
                return (sumSq / count) - (mean * mean);
            },

            detectTextZones(gray, W, H) {
                const cols = 8, rows = 10;
                const cellW = Math.floor(W / cols), cellH = Math.floor(H / rows);
                const zones = [];
                let anyDetected = false;
                for (let row = 0; row < rows; row++) {
                    for (let col = 0; col < cols; col++) {
                        const sx = col * cellW, sy = row * cellH;
                        const ex = Math.min(sx + cellW, W - 1), ey = Math.min(sy + cellH, H - 1);
                        let transitions = 0, samples = 0;
                        for (let y = sy; y < ey; y++) {
                            let prev = gray[y * W + sx];
                            for (let x = sx + 1; x < ex; x++) {
                                const cur = gray[y * W + x];
                                if (Math.abs(cur - prev) > 20) transitions++;
                                prev = cur; samples++;
                            }
                        }
                        const density = samples > 0 ? (transitions / samples) * 100 : 0;
                        if (density > this.textDetectionThreshold) {
                            anyDetected = true;
                            zones.push({ nx: sx / W, ny: sy / H, nw: (ex - sx) / W, nh: (ey - sy) / H, density });
                        }
                    }
                }
                this.textZonesDetected = anyDetected;
                return zones;
            },

            renderTextZones(zones) {
                const W = this.displayWidth, H = this.displayHeight;
                this.textZones = zones.map((z, i) => ({
                    key: 'tz-' + i,
                    x: z.nx * W, y: z.ny * H, w: z.nw * W, h: z.nh * H,
                    color: z.density > this.textDetectionThreshold * 1.8 ? '#fbbf24' : '#60a5fa',
                }));
            },
        };
    }
    </script>
</x-app-layout>