<x-app-layout>
    <main class="career-page">
        <div class="career-shell">
            <div class="career-topline">
                <span>CIA / Career intelligence</span>
                <span>Candidate workspace</span>
            </div>
            <h1 class="career-title">Skill gap analysis</h1>
            <p class="career-intro">Compare your completed learning and quiz results with the capabilities expected in your next role.</p>

            <nav class="career-tabs" aria-label="Career intelligence">
                <a class="career-tab" href="{{ route('cia_career.skill-gap') }}" aria-current="page">Career</a>
            </nav>

            <div class="career-grid">
                <section class="career-panel" aria-labelledby="role-heading">
                    <h2 id="role-heading">Target role</h2>
                    <form id="skill-gap-form" action="{{ route('candidate.cia.career.skill-gap') }}" method="post">
                        @csrf
                        <label class="career-label" for="target_role">Choose a role to assess against</label>
                        <select class="career-select" id="target_role" name="target_role">
                            <option>Full Stack Developer</option>
                            <option>Frontend Developer</option>
                            <option>Backend Developer</option>
                            <option>Data Analyst</option>
                            <option>DevOps Engineer</option>
                        </select>
                        <button class="career-submit" type="submit">
                            <span aria-hidden="true">&gt;</span>
                            <span>Analyze skill gaps</span>
                        </button>
                        <p class="career-status" id="analysis-status" role="status" aria-live="polite">Your learning records are private to your account.</p>
                    </form>
                </section>

                <section class="career-panel" aria-labelledby="profile-heading">
                    <h2 id="profile-heading">Role readiness</h2>
                    <div class="career-summary">
                        <span class="career-score" id="readiness-score">--</span>
                        <span class="career-score-caption">overall match</span>
                    </div>
                    <div class="career-chart-wrap">
                        <canvas id="skill-radar" aria-label="Radar chart comparing your skills with role expectations"></canvas>
                    </div>
                    <p class="career-empty" id="profile-empty">Run an analysis to see your skill profile.</p>
                </section>
            </div>

            <div class="career-results">
                <section class="career-panel" aria-labelledby="gaps-heading">
                    <h2 id="gaps-heading">Priority gaps</h2>
                    <ul class="career-list" id="gap-list"><li class="career-empty">No analysis yet.</li></ul>
                </section>
                <section class="career-panel" aria-labelledby="learning-heading">
                    <h2 id="learning-heading">Priority learning</h2>
                    <ul class="career-list" id="learning-list"><li class="career-empty">Learning priorities will appear here.</li></ul>
                </section>
            </div>
        </div>
    </main>

    <script>
        (() => {
            const form = document.getElementById('skill-gap-form');
            const status = document.getElementById('analysis-status');
            const button = form.querySelector('button');
            let chart;

            const addItem = (list, title, detail = '') => {
                const item = document.createElement('li');
                const heading = document.createElement('strong');
                heading.textContent = title;
                item.append(heading);
                if (detail) {
                    const text = document.createElement('span');
                    text.textContent = detail;
                    item.append(text);
                }
                list.append(item);
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                button.disabled = true;
                status.dataset.state = '';
                    status.textContent = 'Reviewing your completed lessons and quiz scores...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                        },
                        body: JSON.stringify({ target_role: form.target_role.value }),
                    });
                    const result = await response.json();
                    if (!response.ok) {
                        throw new Error(result.message || 'Analysis could not be completed.');
                    }

                    document.getElementById('readiness-score').textContent = `${result.score}%`;
                    const skills = result.skill_profile || [];
                    const gapItems = result.gaps || [];
                    const labels = [...new Set([
                        ...skills.map((item) => item.skill),
                        ...gapItems.map((item) => item.skill),
                    ])];
                    const values = labels.map((label) => skills.find((item) => item.skill === label)?.score || 0);
                    const required = labels.map((label) => {
                        const gap = gapItems.find((item) => item.skill === label);
                        return gap ? gap.required : 75;
                    });

                    if (!labels.length) {
                        labels.push('No learning records');
                        values.push(0);
                        required.push(75);
                    }

                    if (chart) chart.destroy();
                    chart = new window.Chart(document.getElementById('skill-radar'), {
                        type: 'radar',
                        data: {
                            labels,
                            datasets: [
                                { label: 'Your profile', data: values, borderColor: '#16735f', backgroundColor: 'rgba(22, 115, 95, .17)', pointBackgroundColor: '#16735f', borderWidth: 2 },
                                { label: 'Role benchmark', data: required, borderColor: '#db725b', backgroundColor: 'rgba(219, 114, 91, .08)', pointBackgroundColor: '#db725b', borderDash: [5, 4], borderWidth: 2 },
                            ],
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: { r: { min: 0, max: 100, ticks: { stepSize: 25, backdropColor: 'transparent' }, pointLabels: { color: '#18312d', font: { size: 12 } }, grid: { color: '#dce3d9' }, angleLines: { color: '#dce3d9' } } },
                            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, color: '#65736e' } } },
                        },
                    });

                    const gaps = document.getElementById('gap-list');
                    gaps.replaceChildren();
                    result.gaps.length
                        ? result.gaps.forEach((gap) => addItem(gaps, gap.skill, `${gap.current}% current / ${gap.required}% target`))
                        : addItem(gaps, 'No critical gaps found', 'Keep building evidence through completed lessons and quizzes.');

                    const learning = document.getElementById('learning-list');
                    learning.replaceChildren();
                    result.priority_learning.length
                        ? result.priority_learning.forEach((item) => addItem(learning, item))
                        : addItem(learning, 'Keep your learning momentum', 'Complete another lesson and quiz to refresh your profile.');

                    document.getElementById('profile-empty').hidden = skills.length > 0;
                    status.textContent = `Analysis complete for ${form.target_role.value}.`;
                } catch (error) {
                    status.dataset.state = 'error';
                    status.textContent = error.message || 'Network error. Please try again.';
                } finally {
                    button.disabled = false;
                }
            });
        })();
    </script>
</x-app-layout>