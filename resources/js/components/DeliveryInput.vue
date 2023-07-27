<template>
    <!--
            V $table->text('BTPLArtikelCode');
            V $table->text('BTPLTekst');
            V $table->text('BTPLVerpakkingsCode');
            V $table->text('BTPLKaliber');
            V $table->text('BTPLArticleRemark');
            V $table->text('BTPLArticleCollie');
            V $table->text('BTPLArticleWeight');
    -->
    <div>
        <div class="delivery-input">
            <!--<input readonly class="read-only" type="text" id="artikelcode" v-model="article.BTPLArtikelCode" placeholder="Artikelcode" />-->
            <input readonly class="read-only" type="text" id="artikelnaam" v-model="article.BTPLTekst" placeholder="Artikelnaam" />
            <input readonly class="read-only" type="text" id="artikelkaliber" v-model="article.BTPLKaliber" placeholder="Artikelkaliber" />
            <input readonly class="read-only" type="text" id="artikelverpakking" v-model="article.BTPLVerpakkingsCode" placeholder="Artikelverpakking" />
            <input type="number" id="collie" placeholder="# collie" v-model="article.BTPLArticleCollie"/>
            <input readonly class="read-only" type="text" id="gewicht" placeholder="Totale gewicht" v-model="article.BTPLArticleWeight" />
            <input type="text" id="remark" placeholder="Opmerking" v-model="article.BTPLArticleRemark"/>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'DeliveryInput',
    props: {
      template: {
        type: Object,
        required: true
      }
    },
    data(){
      return{
        article: {
          BTPLArtikelCode: this.template.BTPLArtikelCode,
          BTPLVerpakkingsCode: this.template.BTPLVerpakkingsCode,
          BTPLKaliber: this.template.BTPLKaliber,
          BTPLTekst: this.template.BTPLTekst,
          BTPLArticleCollie: 0,
          BTPLArticleWeight: 0,
          BTPLArticleRemark: '',
          BTPLLeverCode: this.template.BTPLLeverCode,
          BTPLSchema: this.template.BTPLSchema,
          BTPLLijnnr: this.template.BTPLLijnnr,
        }
      }
    },
    watch: {
      article: {
        handler(val){
          this.article.BTPLArticleWeight = parseInt(val.BTPLArticleCollie) * parseInt(this.template.BTPLTotaalGewicht);
          this.$emit('articleChanged', val);
        },
        deep: true
      },
    }
  }
</script>