Vue.component("container-item-list-direct-cross", {

    delimiters: ["${", "}"],

    props:
    {
        template:
        {
            type: String,
            default: "#vue-container-item-list-direct-cross"
        },
        items:
        {
            type: Array,
            default: []
        },
        mainprice:
        {
            type: Number,
            default: 0
        },
        mainid:
        {
            type: Number,
            default: 0
        }
    },

    created()
    {
        this.$options.template = this.template;
    },

    mounted()
    {
        this.$nextTick(() =>
        {
            if (this.items.length > 4)
            {
                this.initializeCarousel();
            }
        });
    },

    methods:
    {
        formatPrice(value)
        {
            const val = (value / 1).toFixed(2).replace(".", ",");

            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        getEEK(itemProperties)
        {
            for (var i = 0; i < itemProperties.length; i++)
            {
                if (itemProperties[i].group.backendName == "Energieeffizienzklasse")
                {
                    if (itemProperties[i].property.backendName == "energy_efficiency_class")
                        return "https://www.etc-shop.de/layout/callisto_3/img/ccr_eek/eek_" + itemProperties[i].selection.name.replace("+++++", "PPPPP").replace("++++", "PPPP").replace("+++", "PPP").replace("++", "PP").replace("+", "P") + ".png";
                }
            }
            return "";
        },

        getLabelUrl(itemProperties)
        {
            for (var i = 0; i < itemProperties.length; i++)
            {
                if (itemProperties[i].group.backendName == "Energieeffizienzklasse")
              {
                    if (itemProperties[i].property.backendName == "Label")
                        return itemProperties[i].texts.value;
                }
            }
            return "";
        },

        changeItemForBasket(item)
        {
            // this.variation = item.data.variation;
            // this.$store.commit("setVariation", item.data.variation);
            vue.$refs.addtobasketref.updateItemWithCrossSelling(item.data.variation.id);
        },

        compare(first, second)
        {
            if (first.position < second.position)
                return -1;
            if (first.position > second.position)
                return 1;
            return 0;
        },

        getLastItemURL(pics)
        {
            var arr = pics.slice().sort(function(first, second)
{
                return first.position - second.position;
            });

            return arr[arr.length - 1].urlPreview;
        }
    },
    watch:
    {
        variation(newValue, oldValue)
        {
            this.$store.commit("setVariation", newValue);
        }
    }
});
