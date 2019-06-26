Vue.component("crossselling-item", {

    delimiters: ["${", "}"],

    template: "#vue-crossselling-itemETC",

    props: [
        "decimalCount",
        "itemData",
        "imageUrlAccessor"
    ],

    data()
    {
        return {
            recommendedRetailPrice: 0,
            variationRetailPrice  : 0
        };
    },

    computed:
    {
        /**
         * returns itemData.item.storeSpecial
         */
        storeSpecial()
        {
            return this.itemData.item.storeSpecial;
        },

        /**
         * returns itemData.texts[0]
         */
        texts()
        {
            return this.itemData.texts;
        },

        firstImageByPosition()
        {
            let pos = 10000;
            let retImg = "";

            for (var i = 0; i < this.itemData.images.all.length; i++)
            {
                if (this.itemData.images.all[i].position < pos && this.itemData.images.all[i].position != 0)
              {
                    pos = this.itemData.images.all[i].position;
                    retImg = this.itemData.images.all[i].urlMiddle;
                }
            }
            return retImg;
        },

        ...Vuex.mapState({
            showNetPrices: state => state.basket.showNetPrices
        })
    },

    created()
    {
        if (this.itemData.prices.rrp)
        {
            this.recommendedRetailPrice = this.itemData.prices.rrp.price.value;
        }
        this.variationRetailPrice = this.itemData.prices.default.price.value;
    },

    methods:
    {
        loadFirstImage()
        {
            const categoryImageCarousel = this.$refs.categoryImageCarousel;

            if (categoryImageCarousel)
            {
                categoryImageCarousel.loadFirstImage();
            }
        }
    }

});
