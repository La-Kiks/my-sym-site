import axios from 'axios';
export default class Like {
    constructor(likeElements) {
        this.likeElements = likeElements;

        if(this.likeElements){
            this.init();
        }
    }

    init() {
        this.likeElements.map(element => {
            element.addEventListener('click', this.onClick);
        })
    }

    onClick(event) {
        event.preventDefault();
        const url = this.href;

        axios.get(url).then(res => {
            const nb = res.data.nbLike;
            const span = this.querySelector('span');

            this.dataset.nb = nb;
            span.innerHTML = nb + ' J\'aime';

            const thumbsUpFilled = this.querySelector('svg.filled');
            const thumbsUpUnfilled = this.querySelector('svg.unfilled');

            thumbsUpFilled.classList.toggle('hidden');
            thumbsUpUnfilled.classList.toggle('hidden');
        })
    }

}