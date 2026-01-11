using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using FamilyTreeSystem.Models.Enums;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 家庭成员模型
    /// </summary>
    public class FamilyMember
    {
        /// <summary>
        /// 家庭成员主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 姓名，必填，最长10个字符
        /// </summary>
        [Required]
        [StringLength(10)]
        [DisplayName("姓名")]
        public required string FullName { get; set; }

        /// <summary>
        /// 曾用名，最长20个字符
        /// </summary>
        [StringLength(20)]
        [DisplayName("曾用名")]
        public string? FormerName { get; set; }

        /// <summary>
        /// 性别，必填，男或女
        /// </summary>
        [Required]
        [StringLength(6)]
        [DisplayName("性别")]
        public required string Gender { get; set; }//男,女

        /// <summary>
        /// 是否在世，默认为true（默认在世）
        /// </summary>
        [DisplayName("是否在世")]
        public bool IsAlive { get; set; } = true;

        /// <summary>
        /// 头像图片URL，最长255个字符
        /// </summary>
        [StringLength(255)]
        [DisplayName("头像URL")]
        public string? ImageUrl { get; set; }

        /// <summary>
        /// 手机号，最长20个字符
        /// </summary>
        [StringLength(20)]
        [DisplayName("手机号")]
        public string? PhoneNumber { get; set; }

        /// <summary>
        /// 证件地址，最长255个字符
        /// </summary>
        [StringLength(255)]
        [DisplayName("证件地址")]
        public string? CertificateAddress { get; set; }

        /// <summary>
        /// 身份证号，最长18个字符
        /// </summary>
        [StringLength(18)]
        [DisplayName("身份证号")]
        public string? IDCardNumber { get; set; }

        /// <summary>
        /// 教育程度，最长50个字符
        /// </summary>
        [StringLength(50)]
        [DisplayName("教育程度")]
        public string? EducationLevel { get; set; }

        /// <summary>
        /// 毕业院校，最长100个字符
        /// </summary>
        [StringLength(100)]
        [DisplayName("毕业院校")]
        public string? GraduateSchool { get; set; }

        /// <summary>
        /// 工作单位，最长100个字符
        /// </summary>
        [StringLength(100)]
        [DisplayName("工作单位")]
        public string? WorkUnit { get; set; }

        /// <summary>
        /// 功德款，decimal类型
        /// </summary>
        [DisplayName("功德款")]
        [Column(TypeName = "decimal(18,2)")]
        public decimal? MeritContribution { get; set; }

        /// <summary>
        /// 配偶ID，外键
        /// </summary>
        [DisplayName("配偶ID")]
        public int? SpouseId { get; set; }

        /// <summary>
        /// 父亲ID，外键
        /// </summary>
        [DisplayName("父亲ID")]
        public int? FatherId { get; set; }

        /// <summary>
        /// 母亲ID，外键
        /// </summary>
        [DisplayName("母亲ID")]
        public int? MotherId { get; set; }
        
        /// <summary>
        /// 与配偶关系类型（原配/再婚）
        /// </summary>
        [DisplayName("与配偶关系")]
        public RelationshipType? SpouseRelationType { get; set; }
        /// <summary>
        /// 字辈ID，外键
        /// </summary>
        [DisplayName("字辈ID")]
        public int? ZiweiId { get; set; }
        /// <summary>
        /// 世代，用于标识家族传承代数
        /// </summary>
        [DisplayName("世代")]
        public int? Generation { get; set; }
        /// <summary>
        /// 出生顺序，用于标识同一父母下的子女顺序
        /// </summary>
        [DisplayName("出生顺序")]
        public int? BirthOrder { get; set; }
        /// <summary>
        /// 宗族ID，外键
        /// </summary>
        [DisplayName("宗族ID")]
        public int? ClanId { get; set; }

        /// <summary>
        /// 个人简介，用于描述成员生平事迹
        /// </summary>
        [DisplayName("个人简介")]
        public string? Description { get; set; }

        /// <summary>
        /// 创建人ID，必填
        /// </summary>
        [Required]
        [DisplayName("创建人ID")]
        public int UserId { get; set; }

        /// <summary>
        /// 创建时间，默认当前时间
        /// </summary>
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        /// <summary>
        /// 更新时间，默认当前时间
        /// </summary>
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 配偶导航属性
        /// </summary>
        [ForeignKey("SpouseId")]
        public FamilyMember? Spouse { get; set; }

        /// <summary>
        /// 父亲导航属性
        /// </summary>
        [ForeignKey("FatherId")]
        public FamilyMember? Father { get; set; }

        /// <summary>
        /// 母亲导航属性
        /// </summary>
        [ForeignKey("MotherId")]
        public FamilyMember? Mother { get; set; }

        /// <summary>
        /// 字辈导航属性
        /// </summary>
        [ForeignKey("ZiweiId")]
        public Ziwei? Ziwei { get; set; }

        /// <summary>
        /// 创建人导航属性
        /// </summary>
        [ForeignKey("UserId")]
        public User? User { get; set; }
        /// <summary>
        /// 宗族导航属性
        /// </summary>
        [ForeignKey("ClanId")]
        public Clan? Clan { get; set; }

        /// <summary>
        /// 子女导航属性集合
        /// </summary>
        public ICollection<FamilyMember> Children { get; set; } = new List<FamilyMember>();

        /// <summary>
        /// 作为父母的子女关系集合
        /// </summary>
        public ICollection<ChildRelation> ChildRelations { get; set; } = new List<ChildRelation>();

        /// <summary>
        /// 兄弟姐妹关系集合
        /// </summary>
        public ICollection<SiblingRelation> SiblingRelations1 { get; set; } = new List<SiblingRelation>();

        /// <summary>
        /// 兄弟姐妹关系集合（反向）
        /// </summary>
        public ICollection<SiblingRelation> SiblingRelations2 { get; set; } = new List<SiblingRelation>();

        

        

        

        // 获取性别中文显示
        public string GenderDisplay => Gender switch
        {
            "male" or "Male" => "男",
            "female" or "Female" => "女",
            _ => "男" // 默认显示为男
        };

        /// <summary>
        /// 获取所有兄弟姐妹（作为成员1的关系）
        /// </summary>
        public IEnumerable<FamilyMember> GetSibling1()
        {
            return SiblingRelations1.Select(r => r.Member2);
        }

        /// <summary>
        /// 获取所有兄弟姐妹（作为成员2的关系）
        /// </summary>
        public IEnumerable<FamilyMember> GetSibling2()
        {
            return SiblingRelations2.Select(r => r.Member1);
        }

        /// <summary>
        /// 获取所有兄弟姐妹
        /// </summary>
        public IEnumerable<FamilyMember> GetAllSiblings()
        {
            return GetSibling1().Concat(GetSibling2());
        }

        /// <summary>
        /// 获取兄弟姐妹关系类型
        /// </summary>
        public IEnumerable<(FamilyMember sibling, string relationType)> GetSiblingsWithRelation()
        {
            var siblings1 = SiblingRelations1.Select(r => (r.Member2, r.RelationshipTypeDisplay));
            var siblings2 = SiblingRelations2.Select(r => (r.Member1, r.RelationshipTypeDisplay));
            return siblings1.Concat(siblings2);
        }

        /// <summary>
        /// 获取与配偶的关系类型显示
        /// </summary>
        public string SpouseRelationTypeDisplay => SpouseRelationType switch
        {
            RelationshipType.PrimarySpouse => "原配",
            RelationshipType.SecondarySpouse => "再婚",
            _ => "未知"
        };
    }
}