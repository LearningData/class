<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a single table with with one to several 
	 assessment grades and a short written comment; the table may span multiple pages -->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
	<table>
	  <tr>
		<td colspan="2">
		  <label>
			Student
		  </label>
		  <xsl:value-of select="forename/value/text()" />&#160;
		  <xsl:value-of select="surname/value/text()" />&#160;
		  <xsl:value-of select="middlenames/value/text()" />
		</td>
		<td>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />
		  <xsl:text>&#160;&#160;&#160;&#160;</xsl:text>
		</td>
		<td>
		  <label>
			Date
		  </label>
		<xsl:value-of select="reports/publishdate/text()" />
		</td>
	  </tr>
	</table>
	<xsl:apply-templates select="reports"/>
</xsl:template>

<xsl:template match="reports">
  <table class="grades">
	<tr>
	  <th>
		<xsl:text>Subject</xsl:text>
	  </th>
	  <xsl:call-template name="assheader" />
	  <th><label>Subject teacher's comment.</label></th>
	</tr>

	<xsl:apply-templates select="report">
	  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
	</xsl:apply-templates>

  </table>

<div class='spacer'></div>

<xsl:apply-templates select="summaries/summary" />

</xsl:template>

<xsl:template match="reports/summaries/summary">
  <xsl:if test="description/type='com' and comments/text()!=' '" >
	<div class="sumcomment">
	  <xsl:value-of select="description/value/text()" /><br />
	  <xsl:apply-templates select="comments"/>
	</div>
  </xsl:if>
</xsl:template>

<xsl:template match="report">
<tr>
  <th>
	<xsl:choose>
	  <xsl:when test="component/value/text()!=' '">
		<xsl:value-of select="component/value/text()" />
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:value-of select="subject/value/text()" />
	  </xsl:otherwise>
	</xsl:choose>
  </th>


  <xsl:call-template name="asscell" />

  <td class="subject-comment">
	<xsl:apply-templates select="comments"/>
	<xsl:text>&#160;</xsl:text>
  </td>
</tr>
</xsl:template>

<xsl:template match="comments">
  <xsl:apply-templates select="comment"/>
</xsl:template>

<xsl:template match="comment">
  <p class="comment-text">
	<xsl:value-of select="text/value/text()" />
	<xsl:text>&#160;(</xsl:text>
	<xsl:value-of select="teacher/value/text()" />
	<xsl:text>)&#160;</xsl:text>
  </p>
</xsl:template>


<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		  <label>
	  <xsl:value-of select="$asslabel[$index]" />  
	  <xsl:text>&#160; </xsl:text>
		  </label>
	</th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(../asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='../asstable/ass/label' />

  <xsl:if test="$index &lt; $maxindex">
	<td>
	  <xsl:value-of select="assessments/assessment[printlabel/value=$asslabel[$index]]/result/value/text()" />  
	  <xsl:text>&#160; </xsl:text>
	</td>
    <xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


</xsl:stylesheet>
