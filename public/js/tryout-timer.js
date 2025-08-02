class TryoutTimer {
    constructor(tryoutId, duration, startTime = null) {
        this.tryoutId = tryoutId;
        this.duration = duration * 60; // Convert minutes to seconds
        this.startTime = startTime || Date.now();
        this.timeLeft = this.duration;
        this.interval = null;
        this.onTimeUp = null;
        this.onTick = null;
        
        this.init();
    }
    
    init() {
        // Load from session storage if exists
        const stored = sessionStorage.getItem(`tryout_${this.tryoutId}`);
        if (stored) {
            const data = JSON.parse(stored);
            this.startTime = data.startTime;
            this.timeLeft = data.timeLeft;
        } else {
            // Save initial state
            this.saveState();
        }
        
        this.updateDisplay();
        this.start();
    }
    
    start() {
        this.interval = setInterval(() => {
            this.timeLeft--;
            this.updateDisplay();
            this.saveState();
            
            if (this.onTick) {
                this.onTick(this.timeLeft);
            }
            
            if (this.timeLeft <= 0) {
                this.stop();
                if (this.onTimeUp) {
                    this.onTimeUp();
                }
            }
        }, 1000);
    }
    
    stop() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }
    
    pause() {
        this.stop();
    }
    
    resume() {
        if (!this.interval) {
            this.start();
        }
    }
    
    updateDisplay() {
        const minutes = Math.floor(this.timeLeft / 60);
        const seconds = this.timeLeft % 60;
        const timerDisplay = document.getElementById('timer');
        const timerProgress = document.getElementById('timer-progress');
        
        if (timerDisplay) {
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        if (timerProgress) {
            const progressPercent = (this.timeLeft / this.duration) * 100;
            timerProgress.style.width = `${progressPercent}%`;
            
            // Change color based on time remaining
            if (progressPercent <= 20) {
                timerProgress.className = 'progress-bar bg-danger';
            } else if (progressPercent <= 50) {
                timerProgress.className = 'progress-bar bg-warning';
            } else {
                timerProgress.className = 'progress-bar bg-info';
            }
        }
    }
    
    saveState() {
        const state = {
            startTime: this.startTime,
            timeLeft: this.timeLeft
        };
        sessionStorage.setItem(`tryout_${this.tryoutId}`, JSON.stringify(state));
    }
    
    clearState() {
        sessionStorage.removeItem(`tryout_${this.tryoutId}`);
    }
    
    getTimeLeft() {
        return this.timeLeft;
    }
    
    getFormattedTime() {
        const minutes = Math.floor(this.timeLeft / 60);
        const seconds = this.timeLeft % 60;
        return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
}

// Auto-save functionality
class TryoutAutoSave {
    constructor(tryoutId, questionId) {
        this.tryoutId = tryoutId;
        this.questionId = questionId;
        this.autoSaveInterval = null;
        this.lastAnswer = null;
    }
    
    start() {
        // Auto-save every 30 seconds
        this.autoSaveInterval = setInterval(() => {
            this.autoSave();
        }, 30000);
        
        // Auto-save on page unload
        window.addEventListener('beforeunload', () => {
            this.autoSave();
        });
    }
    
    stop() {
        if (this.autoSaveInterval) {
            clearInterval(this.autoSaveInterval);
            this.autoSaveInterval = null;
        }
    }
    
    autoSave() {
        const currentAnswer = this.getCurrentAnswer();
        if (currentAnswer && JSON.stringify(currentAnswer) !== JSON.stringify(this.lastAnswer)) {
            this.saveAnswer(currentAnswer, true);
            this.lastAnswer = currentAnswer;
        }
    }
    
    getCurrentAnswer() {
        const questionType = document.querySelector('input[name="jawaban"]') ? 'single' : 'multiple';
        
        if (questionType === 'single') {
            const radio = document.querySelector('input[name="jawaban"]:checked');
            return radio ? [radio.value] : [];
        } else {
            const checkboxes = document.querySelectorAll('input[name="jawaban[]"]:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }
    }
    
    saveAnswer(answer, isAutoSave = false) {
        const formData = new FormData();
        formData.append('jawaban', JSON.stringify(answer));
        formData.append('soal_id', this.questionId);
        
        fetch(`/tryout/${this.tryoutId}/submit-answer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && !isAutoSave) {
                this.showNotification('Jawaban berhasil disimpan!');
                this.updateQuestionStatus(true);
            }
        })
        .catch(error => {
            console.error('Error saving answer:', error);
            if (!isAutoSave) {
                this.showNotification('Terjadi kesalahan saat menyimpan jawaban', 'error');
            }
        });
    }
    
    showNotification(message, type = 'success') {
        const notification = document.getElementById('auto-save-notification');
        if (notification) {
            const alert = notification.querySelector('.alert');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.innerHTML = `
                <i class="fa fa-${type === 'success' ? 'check' : 'exclamation-triangle'}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;
            
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
    }
    
    updateQuestionStatus(isAnswered) {
        const currentQuestionNumber = document.querySelector('.question-number.current');
        if (currentQuestionNumber) {
            const questionNumber = currentQuestionNumber.textContent.trim();
            const questionElement = document.querySelector(`.question-number[href*="question=${questionNumber}"]`);
            if (questionElement) {
                questionElement.className = `question-number ${isAnswered ? 'answered' : 'unanswered'}`;
            }
        }
    }
}

// Global tryout timer instance
let tryoutTimer = null;
let tryoutAutoSave = null;

// Initialize tryout functionality
function initTryout(tryoutId, duration, questionId) {
    // Initialize timer
    tryoutTimer = new TryoutTimer(tryoutId, duration);
    
    // Set up time up callback
    tryoutTimer.onTimeUp = function() {
        alert('Waktu pengerjaan tryout telah habis!');
        window.location.href = `/tryout/${tryoutId}/finish`;
    };
    
    // Initialize auto-save
    if (questionId) {
        tryoutAutoSave = new TryoutAutoSave(tryoutId, questionId);
        tryoutAutoSave.start();
    }
}

// Manual save answer function
function saveAnswer() {
    if (tryoutAutoSave) {
        const answer = tryoutAutoSave.getCurrentAnswer();
        tryoutAutoSave.saveAnswer(answer, false);
    }
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (tryoutAutoSave) {
        tryoutAutoSave.stop();
    }
    if (tryoutTimer) {
        tryoutTimer.stop();
    }
}); 