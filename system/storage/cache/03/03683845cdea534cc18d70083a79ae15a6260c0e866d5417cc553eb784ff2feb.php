<?php

/* extension/module/restapi.twig */
class __TwigTemplate_07087dcfa23abd1eb8d8f1bad109514343168be8afcc12ad030aa742e09968f7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo (isset($context["header"]) ? $context["header"] : null);
        echo (isset($context["column_left"]) ? $context["column_left"] : null);
        echo "
<div id=\"content\">
  <div class=\"page-header\">
    <div class=\"container-fluid\">
      <div class=\"pull-right\">
        <button type=\"submit\" form=\"form-module\" data-toggle=\"tooltip\" title=\"";
        // line 6
        echo (isset($context["button_save"]) ? $context["button_save"] : null);
        echo "\" class=\"btn btn-primary\"><i class=\"fa fa-save\"></i></button>
        <a href=\"";
        // line 7
        echo (isset($context["cancel"]) ? $context["cancel"] : null);
        echo "\" data-toggle=\"tooltip\" title=\"";
        echo (isset($context["button_cancel"]) ? $context["button_cancel"] : null);
        echo "\" class=\"btn btn-default\"><i class=\"fa fa-reply\"></i></a></div>
      <h1>";
        // line 8
        echo (isset($context["heading_title"]) ? $context["heading_title"] : null);
        echo "</h1>
      <ul class=\"breadcrumb\">
        ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["breadcrumbs"]) ? $context["breadcrumbs"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["breadcrumb"]) {
            // line 11
            echo "        <li><a href=\"";
            echo $this->getAttribute($context["breadcrumb"], "href", array());
            echo "\">";
            echo $this->getAttribute($context["breadcrumb"], "text", array());
            echo "</a></li>
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['breadcrumb'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 13
        echo "      </ul>
    </div>
  </div>
  <div class=\"container-fluid\">
    ";
        // line 17
        if ((isset($context["error_warning"]) ? $context["error_warning"] : null)) {
            // line 18
            echo "    <div class=\"alert alert-danger alert-dismissible\"><i class=\"fa fa-exclamation-circle\"></i> ";
            echo (isset($context["error_warning"]) ? $context["error_warning"] : null);
            echo "
      <button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>
    </div>
    ";
        }
        // line 22
        echo "    <div class=\"panel panel-default\">
      <div class=\"panel-heading\">
        <h3 class=\"panel-title\"><i class=\"fa fa-pencil\"></i> ";
        // line 24
        echo (isset($context["text_edit"]) ? $context["text_edit"] : null);
        echo "</h3>
      </div>
      <div class=\"panel-body\">
        <form action=\"";
        // line 27
        echo (isset($context["action"]) ? $context["action"] : null);
        echo "\" method=\"post\" enctype=\"multipart/form-data\" id=\"form-module\" class=\"form-horizontal\">
          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-status\">";
        // line 29
        echo (isset($context["entry_status"]) ? $context["entry_status"] : null);
        echo "</label>
            <div class=\"col-sm-10\">
              <select name=\"module_restapi_status\" id=\"input-status\" class=\"form-control\">
                ";
        // line 32
        if ((isset($context["module_restapi_status"]) ? $context["module_restapi_status"] : null)) {
            // line 33
            echo "                <option value=\"1\" selected=\"selected\">";
            echo (isset($context["text_enabled"]) ? $context["text_enabled"] : null);
            echo "</option>
                <option value=\"0\">";
            // line 34
            echo (isset($context["text_disabled"]) ? $context["text_disabled"] : null);
            echo "</option>
                ";
        } else {
            // line 36
            echo "                <option value=\"1\">";
            echo (isset($context["text_enabled"]) ? $context["text_enabled"] : null);
            echo "</option>
                <option value=\"0\" selected=\"selected\">";
            // line 37
            echo (isset($context["text_disabled"]) ? $context["text_disabled"] : null);
            echo "</option>
                ";
        }
        // line 39
        echo "              </select>
            </div>
          </div>

          <div class=\"form-group\">
            <label class=\"col-sm-2 control-label\" for=\"input-entry-client_id\">";
        // line 44
        echo (isset($context["entry_client_id"]) ? $context["entry_client_id"] : null);
        echo "</label>
            <div class=\"col-sm-10\">
              <input class=\"form-control\" type=\"text\" name=\"module_restapi_client_id\" value=\"";
        // line 46
        echo (isset($context["module_restapi_client_id"]) ? $context["module_restapi_client_id"] : null);
        echo "\" />
            </div>
          </div>

          <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-entry-client_secret\">";
        // line 51
        echo (isset($context["entry_client_secret"]) ? $context["entry_client_secret"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                  <input  class=\"form-control\" type=\"text\" name=\"module_restapi_client_secret\" value=\"";
        // line 53
        echo (isset($context["module_restapi_client_secret"]) ? $context["module_restapi_client_secret"] : null);
        echo "\" />
              </div>
          </div>

          <div class=\"form-group\">
              <label class=\"col-sm-2 control-label\" for=\"input-entry-token_ttl\">";
        // line 58
        echo (isset($context["entry_token_ttl"]) ? $context["entry_token_ttl"] : null);
        echo "</label>
              <div class=\"col-sm-10\">
                  <input  class=\"form-control\" type=\"text\" name=\"module_restapi_token_ttl\" value=\"";
        // line 60
        echo (isset($context["module_restapi_token_ttl"]) ? $context["module_restapi_token_ttl"] : null);
        echo "\" />
              </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
";
        // line 69
        echo (isset($context["footer"]) ? $context["footer"] : null);
    }

    public function getTemplateName()
    {
        return "extension/module/restapi.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  167 => 69,  155 => 60,  150 => 58,  142 => 53,  137 => 51,  129 => 46,  124 => 44,  117 => 39,  112 => 37,  107 => 36,  102 => 34,  97 => 33,  95 => 32,  89 => 29,  84 => 27,  78 => 24,  74 => 22,  66 => 18,  64 => 17,  58 => 13,  47 => 11,  43 => 10,  38 => 8,  32 => 7,  28 => 6,  19 => 1,);
    }
}
/* {{ header }}{{ column_left }}*/
/* <div id="content">*/
/*   <div class="page-header">*/
/*     <div class="container-fluid">*/
/*       <div class="pull-right">*/
/*         <button type="submit" form="form-module" data-toggle="tooltip" title="{{ button_save }}" class="btn btn-primary"><i class="fa fa-save"></i></button>*/
/*         <a href="{{ cancel }}" data-toggle="tooltip" title="{{ button_cancel }}" class="btn btn-default"><i class="fa fa-reply"></i></a></div>*/
/*       <h1>{{ heading_title }}</h1>*/
/*       <ul class="breadcrumb">*/
/*         {% for breadcrumb in breadcrumbs %}*/
/*         <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>*/
/*         {% endfor %}*/
/*       </ul>*/
/*     </div>*/
/*   </div>*/
/*   <div class="container-fluid">*/
/*     {% if error_warning %}*/
/*     <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> {{ error_warning }}*/
/*       <button type="button" class="close" data-dismiss="alert">&times;</button>*/
/*     </div>*/
/*     {% endif %}*/
/*     <div class="panel panel-default">*/
/*       <div class="panel-heading">*/
/*         <h3 class="panel-title"><i class="fa fa-pencil"></i> {{ text_edit }}</h3>*/
/*       </div>*/
/*       <div class="panel-body">*/
/*         <form action="{{ action }}" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">*/
/*           <div class="form-group">*/
/*             <label class="col-sm-2 control-label" for="input-status">{{ entry_status }}</label>*/
/*             <div class="col-sm-10">*/
/*               <select name="module_restapi_status" id="input-status" class="form-control">*/
/*                 {% if module_restapi_status %}*/
/*                 <option value="1" selected="selected">{{ text_enabled }}</option>*/
/*                 <option value="0">{{ text_disabled }}</option>*/
/*                 {% else %}*/
/*                 <option value="1">{{ text_enabled }}</option>*/
/*                 <option value="0" selected="selected">{{ text_disabled }}</option>*/
/*                 {% endif %}*/
/*               </select>*/
/*             </div>*/
/*           </div>*/
/* */
/*           <div class="form-group">*/
/*             <label class="col-sm-2 control-label" for="input-entry-client_id">{{entry_client_id}}</label>*/
/*             <div class="col-sm-10">*/
/*               <input class="form-control" type="text" name="module_restapi_client_id" value="{{module_restapi_client_id}}" />*/
/*             </div>*/
/*           </div>*/
/* */
/*           <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-entry-client_secret">{{entry_client_secret}}</label>*/
/*               <div class="col-sm-10">*/
/*                   <input  class="form-control" type="text" name="module_restapi_client_secret" value="{{module_restapi_client_secret}}" />*/
/*               </div>*/
/*           </div>*/
/* */
/*           <div class="form-group">*/
/*               <label class="col-sm-2 control-label" for="input-entry-token_ttl">{{entry_token_ttl}}</label>*/
/*               <div class="col-sm-10">*/
/*                   <input  class="form-control" type="text" name="module_restapi_token_ttl" value="{{module_restapi_token_ttl}}" />*/
/*               </div>*/
/*           </div>*/
/* */
/*         </form>*/
/*       </div>*/
/*     </div>*/
/*   </div>*/
/* </div>*/
/* {{ footer }}*/
