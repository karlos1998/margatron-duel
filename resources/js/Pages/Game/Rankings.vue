<script setup lang="ts">
import GameTopBar from '@/Components/Game/GameTopBar.vue';
import PlayerSidebar from '@/Components/Game/PlayerSidebar.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { GameSnapshot, PlayerRanking } from '@/types/game';

type Resource<T> = T | { data: T };

const props = defineProps<{
    game: Resource<GameSnapshot>;
    ranking: PlayerRanking;
}>();

const game = computed(() => unwrap(props.game));
const user = computed(() => game.value.user);
const showSettings = ref(false);
const settings = ref({ sound: true, music: false, notifications: true });

const sortButtons = [
    { key: 'score', label: 'Score', disabled: true },
    { key: 'level', label: 'Level', disabled: false },
    { key: 'pve', label: 'PvE', disabled: true },
    { key: 'pvp', label: 'PvP', disabled: true },
    { key: 'progress', label: 'Progress', disabled: true },
] as const;

function unwrap<T extends object>(resource: Resource<T>): T {
    return 'data' in resource ? resource.data : resource;
}

function formatNumber(num?: number): string {
    return (num ?? 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

function closeRankings(): void {
    router.get('/game');
}
</script>

<template>
    <Head title="Rankingi" />

    <div id="game-container">
        <GameTopBar active="rankings" @settings="showSettings = true" />

        <div id="main-content">
            <PlayerSidebar :user="user" read-only />

            <main id="map-area">
                <div class="rankings-view">
                    <div class="ranking-board">
                        <div class="ranking-tabs" aria-label="Sortowanie rankingu">
                            <button
                                v-for="button in sortButtons"
                                :key="button.key"
                                class="ranking-tab"
                                :class="{ active: button.key === props.ranking.activeSort }"
                                type="button"
                                :disabled="button.disabled"
                            >
                                {{ button.label }}
                            </button>
                        </div>

                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Name</th>
                                    <th>Lvl</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="entry in props.ranking.entries"
                                    :key="entry.profileId"
                                    :class="{ current: entry.currentUser }"
                                >
                                    <td>{{ entry.position }}</td>
                                    <td>{{ entry.nick }}</td>
                                    <td>{{ entry.level }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="ranking-controls">
                            <button type="button" disabled>Top 20</button>
                            <button type="button" disabled>Dalej</button>
                            <button type="button" disabled>Wstecz</button>
                            <button type="button" disabled>Pokaż mnie</button>
                            <label>
                                Search:
                                <input type="text" disabled>
                            </label>
                        </div>
                    </div>

                    <aside class="ranking-summary">
                        <div class="ranking-position-box">
                            <span>Your ladder position:</span>
                            <strong>{{ formatNumber(props.ranking.currentPosition) }}</strong>
                        </div>

                        <p>Ranking jest teraz sortowany po poziomie postaci.</p>
                        <p>Score, PvE, PvP i Progress czekają na docelowe dane.</p>
                    </aside>

                    <button class="ranking-close" type="button" @click="closeRankings">Zamknij</button>
                </div>
            </main>
        </div>

        <div v-if="showSettings" class="modal" @click.self="showSettings = false">
            <div class="modal-content settings-modal">
                <h2>Ustawienia</h2>
                <div class="setting-row">
                    <label>Dźwięki</label>
                    <input v-model="settings.sound" type="checkbox">
                </div>
                <div class="setting-row">
                    <label>Muzyka</label>
                    <input v-model="settings.music" type="checkbox">
                </div>
                <div class="setting-row">
                    <label>Powiadomienia</label>
                    <input v-model="settings.notifications" type="checkbox">
                </div>
                <button class="btn-close" type="button" @click="showSettings = false">Zamknij</button>
            </div>
        </div>

        <footer id="game-footer"></footer>
    </div>
</template>
