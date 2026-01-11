using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using FamilyTreeSystem.Models.Enums;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 子女关系模型
    /// </summary>
    [Table("ChildRelations")]
    public class ChildRelation
    {
        /// <summary>
        /// 主键ID
        /// </summary>
        [Key]
        [DisplayName("ID")]
        public int Id { get; set; }

        /// <summary>
        /// 父母ID（父亲或母亲）
        /// </summary>
        [DisplayName("父母ID")]
        public int ParentId { get; set; }

        /// <summary>
        /// 子女ID
        /// </summary>
        [DisplayName("子女ID")]
        public int ChildId { get; set; }

        /// <summary>
        /// 与子女关系类型
        /// </summary>
        [DisplayName("关系类型")]
        public ChildRelationshipType RelationshipType { get; set; }

        /// <summary>
        /// 创建时间
        /// </summary>
        public DateTime CreatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 父母导航属性
        /// </summary>
        [ForeignKey("ParentId")]
        public FamilyMember Parent { get; set; } = null!;

        /// <summary>
        /// 子女导航属性
        /// </summary>
        [ForeignKey("ChildId")]
        public FamilyMember Child { get; set; } = null!;

        /// <summary>
        /// 获取血缘关系类型中文显示
        /// </summary>
        public string RelationshipTypeDisplay => RelationshipType switch
        {
            // 血缘关系类型
            ChildRelationshipType.BiologicalChild => "亲子",
            ChildRelationshipType.AdoptiveSon => "养子",
            ChildRelationshipType.StepSon => "继子",
            ChildRelationshipType.GodSon => "义子",
            ChildRelationshipType.BiologicalDaughter => "亲女",
            ChildRelationshipType.AdoptiveDaughter => "养女",
            ChildRelationshipType.StepDaughter => "继女",
            ChildRelationshipType.GodDaughter => "义女",
            
            // 出生顺序关系类型
            ChildRelationshipType.EldestSon => "长子",
            ChildRelationshipType.SecondSon => "次子",
            ChildRelationshipType.ThirdSon => "三子",
            ChildRelationshipType.FourthSon => "四子",
            ChildRelationshipType.FifthSon => "五子",
            ChildRelationshipType.SixthSon => "六子",
            ChildRelationshipType.SeventhSon => "七子",
            ChildRelationshipType.EighthSon => "八子",
            ChildRelationshipType.NinthSon => "九子",
            ChildRelationshipType.TenthSon => "十子",
            ChildRelationshipType.EldestDaughter => "长女",
            ChildRelationshipType.SecondDaughter => "次女",
            ChildRelationshipType.ThirdDaughter => "三女",
            ChildRelationshipType.FourthDaughter => "四女",
            ChildRelationshipType.FifthDaughter => "五女",
            ChildRelationshipType.SixthDaughter => "六女",
            ChildRelationshipType.SeventhDaughter => "七女",
            ChildRelationshipType.EighthDaughter => "八女",
            ChildRelationshipType.NinthDaughter => "九女",
            ChildRelationshipType.TenthDaughter => "十女",
            _ => "未知关系"
        };
    }
}